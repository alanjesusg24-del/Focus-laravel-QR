<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected QrCodeService $qrCodeService;
    protected NotificationService $notificationService;

    public function __construct(
        QrCodeService $qrCodeService,
        NotificationService $notificationService
    ) {
        $this->qrCodeService = $qrCodeService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new order with QR code
     *
     * @param int $businessId
     * @param array $data
     * @return Order
     */
    public function createOrder(int $businessId, array $data): Order
    {
        return DB::transaction(function () use ($businessId, $data) {
            // Generate folio number
            $folioNumber = $this->generateFolioNumber($businessId);

            // Create order
            $order = Order::create([
                'business_id' => $businessId,
                'folio_number' => $folioNumber,
                'description' => $data['description'] ?? null,
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'pending',
                'mobile_user_id' => $data['mobile_user_id'] ?? null,
            ]);

            // Generate QR code
            $this->qrCodeService->generateQrCodeForOrder($order);

            return $order->fresh();
        });
    }

    /**
     * Update order status
     *
     * @param Order $order
     * @param string $newStatus
     * @param array $additionalData
     * @return Order
     */
    public function updateOrderStatus(Order $order, string $newStatus, array $additionalData = []): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $additionalData) {
            $updateData = ['status' => $newStatus];

            switch ($newStatus) {
                case 'ready':
                    $updateData['ready_at'] = now();

                    // Send notification if mobile user exists
                    if ($order->mobile_user_id) {
                        $this->notificationService->sendOrderReadyNotification($order);
                    }
                    break;

                case 'delivered':
                    $updateData['delivered_at'] = now();
                    break;

                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    $updateData['cancellation_reason'] = $additionalData['cancellation_reason'] ?? 'No reason provided';

                    // Send cancellation notification
                    if ($order->mobile_user_id) {
                        $this->notificationService->sendOrderCancelledNotification($order, $updateData['cancellation_reason']);
                    }
                    break;
            }

            $order->update($updateData);

            return $order->fresh();
        });
    }

    /**
     * Mark order as ready
     *
     * @param Order $order
     * @return Order
     */
    public function markAsReady(Order $order): Order
    {
        if ($order->status !== 'pending') {
            throw new \Exception('Only pending orders can be marked as ready');
        }

        return $this->updateOrderStatus($order, 'ready');
    }

    /**
     * Mark order as delivered
     *
     * @param Order $order
     * @param string $pickupToken
     * @return Order
     */
    public function markAsDelivered(Order $order, string $pickupToken): Order
    {
        if ($order->status !== 'ready') {
            throw new \Exception('Only ready orders can be delivered');
        }

        if ($order->pickup_token !== $pickupToken) {
            throw new \Exception('Invalid pickup token');
        }

        return $this->updateOrderStatus($order, 'delivered');
    }

    /**
     * Cancel order
     *
     * @param Order $order
     * @param string $reason
     * @return Order
     */
    public function cancelOrder(Order $order, string $reason): Order
    {
        if (in_array($order->status, ['delivered', 'cancelled'])) {
            throw new \Exception('Cannot cancel delivered or already cancelled orders');
        }

        return $this->updateOrderStatus($order, 'cancelled', [
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Link order to mobile user via QR scan
     *
     * @param string $qrToken
     * @param int $mobileUserId
     * @return Order
     */
    public function linkOrderToMobileUser(string $qrToken, int $mobileUserId): Order
    {
        $order = $this->qrCodeService->validateQrToken($qrToken);

        if (!$order) {
            throw new \Exception('Invalid or expired QR code');
        }

        $order->mobile_user_id = $mobileUserId;
        $order->save();

        return $order;
    }

    /**
     * Get active orders for a business
     *
     * @param int $businessId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveOrders(int $businessId)
    {
        return Order::where('business_id', $businessId)
            ->whereIn('status', ['pending', 'ready'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get order statistics for a business
     *
     * @param int $businessId
     * @param int $days
     * @return array
     */
    public function getOrderStatistics(int $businessId, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $orders = Order::where('business_id', $businessId)
            ->where('created_at', '>=', $startDate)
            ->get();

        return [
            'total' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'ready' => $orders->where('status', 'ready')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'avg_preparation_time' => $this->calculateAveragePreparationTime($orders),
        ];
    }

    /**
     * Generate folio number for business
     *
     * @param int $businessId
     * @return string
     */
    protected function generateFolioNumber(int $businessId): string
    {
        $business = Business::findOrFail($businessId);
        $lastOrder = Order::where('business_id', $businessId)
            ->latest('order_id')
            ->first();

        $nextNumber = $lastOrder ? (int) substr($lastOrder->folio_number, -4) + 1 : 1;
        $prefix = strtoupper(substr($business->business_name, 0, 3));

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }

    /**
     * Calculate average preparation time
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders
     * @return float|null Minutes
     */
    protected function calculateAveragePreparationTime($orders): ?float
    {
        $completedOrders = $orders->whereIn('status', ['delivered', 'ready'])
            ->filter(fn($order) => $order->ready_at !== null);

        if ($completedOrders->isEmpty()) {
            return null;
        }

        $totalMinutes = 0;
        foreach ($completedOrders as $order) {
            $totalMinutes += $order->created_at->diffInMinutes($order->ready_at);
        }

        return round($totalMinutes / $completedOrders->count(), 2);
    }

    /**
     * Clean up old orders based on business plan retention days
     *
     * @param int $businessId
     * @return int Number of deleted orders
     */
    public function cleanupOldOrders(int $businessId): int
    {
        $business = Business::with('plan')->findOrFail($businessId);
        $retentionDate = now()->subDays($business->plan->retention_days);

        return Order::where('business_id', $businessId)
            ->where('created_at', '<', $retentionDate)
            ->whereIn('status', ['delivered', 'cancelled'])
            ->delete();
    }
}
