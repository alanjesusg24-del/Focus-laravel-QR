<?php

/**
 * Company: CETAM
 * Project: FF
 * File: OrderService.php
 * Created on: 20/10/2025
 * Created by: Alan Jesus Garcia Nava
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 10/11/2025 |
 *   Modified by: Alan Jesus Garcia Nava |
 *   Description: Refactored OrderService |
 */

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
     */
    public function createOrder(int $businessId, array $data): Order
    {
        return DB::transaction(function () use ($businessId, $data) {
            $folioNumber = $this->generateFolioNumber($businessId);

            $order = Order::create([
                'business_id' => $businessId,
                'folio_number' => $folioNumber,
                'business_folio' => $data['business_folio'] ?? null,
                'description' => $data['description'] ?? null,
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'pending',
                'mobile_user_id' => $data['mobile_user_id'] ?? null,
            ]);

            $this->qrCodeService->generateQrCodeForOrder($order);

            return $order->fresh();
        });
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, string $newStatus, array $additionalData = []): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $additionalData) {
            $updateData = match ($newStatus) {
                'ready' => $this->prepareReadyStatusData($order),
                'delivered' => $this->prepareDeliveredStatusData(),
                'cancelled' => $this->prepareCancelledStatusData($order, $additionalData),
                default => ['status' => $newStatus],
            };

            $order->update($updateData);

            return $order->fresh();
        });
    }

    /**
     * Mark order as ready
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
     */
    public function markAsDelivered(Order $order): Order
    {
        if ($order->status !== 'ready') {
            throw new \Exception('Only ready orders can be delivered');
        }

        return $this->updateOrderStatus($order, 'delivered');
    }

    /**
     * Cancel order
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
     */
    protected function generateFolioNumber(int $businessId): string
    {
        $business = Business::findOrFail($businessId);
        $prefix = strtoupper(substr($business->business_name, 0, 3));

        $lastOrder = Order::where('business_id', $businessId)
            ->where('folio_number', 'like', $prefix . '-%')
            ->orderBy('folio_number', 'desc')
            ->lockForUpdate()
            ->first();

        $nextNumber = $lastOrder
            ? ((int) substr($lastOrder->folio_number, strlen($prefix) + 1)) + 1
            : 1;

        $folioNumber = sprintf('%s-%04d', $prefix, $nextNumber);

        $attempt = 0;
        while (Order::where('folio_number', $folioNumber)->exists() && $attempt < 100) {
            $nextNumber++;
            $folioNumber = sprintf('%s-%04d', $prefix, $nextNumber);
            $attempt++;
        }

        return $folioNumber;
    }

    /**
     * Calculate average preparation time
     * Protected helper method following SRP
     */
    protected function calculateAveragePreparationTime($orders): ?float
    {
        $completedOrders = $orders->whereIn('status', ['delivered', 'ready'])
            ->filter(fn(Order $order) => $order->ready_at !== null);

        if ($completedOrders->isEmpty()) {
            return null;
        }

        $totalMinutes = $completedOrders->reduce(
            fn(float $total, Order $order) => $total + $order->created_at->diffInMinutes($order->ready_at),
            0
        );

        return round($totalMinutes / $completedOrders->count(), 2);
    }

    /**
     * Clean up old orders based on business plan retention days
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

    /**
     * Prepare ready status data
     * Private helper method following SRP
     */
    private function prepareReadyStatusData(Order $order): array
    {
        $updateData = [
            'status' => 'ready',
            'ready_at' => now(),
        ];

        if ($order->mobile_user_id) {
            $this->notificationService->sendOrderReadyNotification($order);
        }

        return $updateData;
    }

    /**
     * Prepare delivered status data
     * Private helper method following SRP
     */
    private function prepareDeliveredStatusData(): array
    {
        return [
            'status' => 'delivered',
            'delivered_at' => now(),
        ];
    }

    /**
     * Prepare cancelled status data
     * Private helper method following SRP
     */
    private function prepareCancelledStatusData(Order $order, array $additionalData): array
    {
        $updateData = [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $additionalData['cancellation_reason'] ?? 'No reason provided',
        ];

        if ($order->mobile_user_id) {
            $this->notificationService->sendOrderCancelledNotification($order, $updateData['cancellation_reason']);
        }

        return $updateData;
    }
}
