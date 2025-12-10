{{--
  Company: CETAM
  Project: Focus QR System
  File: orders/associate-error.blade.php
  Description: Vista de error cuando una orden no puede ser asociada vía QR web
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error - Sistema de Órdenes QR</title>

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

        .error-container {
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

        .error-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.6s ease-out 0.2s both;
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.3);
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .error-icon i {
            font-size: 3rem;
            color: white;
            animation: shake 0.5s ease-in-out 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="card border-0 shadow-lg">
            <div class="card-body text-center py-5 px-4">

                <!-- Error Icon -->
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <!-- Error Title -->
                <h2 class="h3 fw-bold text-cetam-danger mb-3">
                    No se pudo procesar la orden
                </h2>

                <!-- Error Message -->
                <div class="alert alert-danger bg-cetam-danger text-white border-0 mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ $message }}
                </div>

                <!-- Instructions -->
                <div class="bg-light rounded p-3 mb-4">
                    <p class="text-muted mb-2 small">
                        <i class="fas fa-lightbulb text-warning me-1"></i>
                        <strong>Posibles razones:</strong>
                    </p>
                    <ul class="text-start text-gray-700 small mb-0">
                        <li>El código QR ha expirado</li>
                        <li>La orden ya fue tomada por alguien más</li>
                        <li>La orden fue cancelada</li>
                        <li>El código QR no es válido</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="d-grid gap-2">
                    <button onclick="window.history.back()" class="btn btn-cetam-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver
                    </button>
                    <button onclick="window.close()" class="btn btn-outline-cetam-primary">
                        <i class="fas fa-times me-2"></i>
                        Cerrar
                    </button>
                </div>

                <!-- Support Info -->
                <div class="mt-4">
                    <p class="text-muted small mb-0">
                        ¿Necesitas ayuda?
                        <a href="mailto:soporte@cetam.mx" class="text-cetam-info text-decoration-none">
                            <i class="fas fa-envelope me-1"></i>
                            Contacta a soporte
                        </a>
                    </p>
                </div>
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
</body>
</html>
