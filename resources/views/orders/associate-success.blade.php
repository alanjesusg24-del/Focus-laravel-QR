{{--
  Company: CETAM
  Project: Focus QR System
  File: orders/associate-success.blade.php
  Description: Vista de confirmación cuando una orden es asociada exitosamente vía QR web
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orden Tomada - Sistema de Órdenes QR</title>

    <!-- Volt CSS -->
    <link type="text/css" href="{{ asset('css/volt.css') }}" rel="stylesheet">
    <link type="text/css" href="{{ asset('css/cetam-colors.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, var(--cetam-primary) 0%, var(--cetam-tertiary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito Sans', sans-serif;
        }

        .success-container {
            max-width: 500px;
            width: 90%;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.6s ease-out 0.2s both;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .checkmark {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            fill: none;
            animation: drawCheck 0.8s ease-out 0.5s both;
        }

        @keyframes drawCheck {
            to {
                stroke-dashoffset: 0;
            }
        }

        .checkmark {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="card border-0 shadow-lg">
            <div class="card-body text-center py-5 px-4">

                <!-- Success Icon -->
                <div class="success-icon">
                    <svg class="checkmark" viewBox="0 0 52 52">
                        <path d="M14 27l8 8 16-16"/>
                    </svg>
                </div>

                <!-- Success Title -->
                <h2 class="h3 fw-bold text-cetam-primary mb-3">
                    {{ $message }}
                </h2>

                <!-- Order Details -->
                <div class="alert alert-success bg-cetam-success text-white border-0 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-barcode me-2 fs-4"></i>
                        <div class="text-center">
                            @if($order->business_folio)
                                <small class="d-block opacity-75">Folio del Negocio</small>
                                <strong class="fs-4 d-block">{{ $order->business_folio }}</strong>
                                <small class="d-block opacity-60 mt-1" style="font-size: 0.75rem;">Sistema: {{ $order->folio_number }}</small>
                            @else
                                <small class="d-block opacity-75">Folio de Orden</small>
                                <strong class="fs-5">{{ $order->folio_number }}</strong>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description if available -->
                @if($order->description)
                <div class="mb-4">
                    <p class="text-muted mb-1"><small>Descripción:</small></p>
                    <p class="text-cetam-primary fw-bold">{{ $order->description }}</p>
                </div>
                @endif

                <!-- Instructions -->
                <div class="bg-light rounded p-3 mb-4">
                    <p class="text-muted mb-2 small">
                        <i class="fas fa-info-circle text-cetam-info me-1"></i>
                        <strong>¿Qué sigue?</strong>
                    </p>
                    <p class="text-gray-700 mb-0 small">
                        La orden ha sido asociada correctamente. Recibirás una notificación cuando esté lista para recoger.
                    </p>
                </div>

                <!-- Action -->
                <button onclick="window.close()" class="btn btn-cetam-primary btn-lg w-100">
                    <i class="fas fa-check me-2"></i>
                    Cerrar
                </button>

                <!-- Footer Info -->
                <p class="text-muted mt-4 mb-0 small">
                    Asociada el {{ $order->associated_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <!-- CETAM Branding -->
        <div class="text-center mt-4">
            <p class="text-white opacity-75 small mb-0">
                <i class="fas fa-qrcode me-1"></i>
                Sistema de Órdenes QR - CETAM
            </p>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Auto-close after 10 seconds (optional) -->
    <script>
        // Uncomment to enable auto-close
        // setTimeout(() => {
        //     window.close();
        // }, 10000);
    </script>
</body>
</html>
