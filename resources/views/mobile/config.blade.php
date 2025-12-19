<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile App Configuration - Order QR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --institutional-blue: #1d4976;
            --institutional-orange: #de5629;
        }
        body {
            background: linear-gradient(135deg, var(--institutional-blue) 0%, #2c5f8f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .config-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 3rem;
            max-width: 600px;
            width: 90%;
        }
        .qr-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            border: 3px solid var(--institutional-orange);
            text-align: center;
            margin: 2rem 0;
        }
        .server-info {
            background: #f8f9fa;
            border-left: 4px solid var(--institutional-orange);
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        .btn-copy {
            background: var(--institutional-orange);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-copy:hover {
            background: #c54923;
            transform: translateY(-2px);
        }
        .status-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            margin-left: 0.5rem;
        }
        h1 {
            color: var(--institutional-blue);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .instructions {
            background: #e7f3ff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .instructions h5 {
            color: var(--institutional-blue);
            margin-bottom: 1rem;
        }
        .instructions ol {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }
        .instructions li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="config-card">
        <div class="text-center">
            <h1>📱 Mobile App Setup</h1>
            <p class="subtitle">Escanea el código QR para configurar tu aplicación móvil</p>
            @if($isNgrok)
                <div class="alert alert-success" role="alert">
                    <strong>🌍 Servidor Público Activo</strong><br>
                    <small>Funciona desde cualquier lugar con internet</small>
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    <strong>📡 Servidor Local</strong><br>
                    <small>Asegúrate de estar en la misma red WiFi</small>
                </div>
            @endif
        </div>

        <div class="qr-container">
            <div id="qrcode"></div>
            <p class="mt-3 mb-0"><strong>API URL</strong></p>
        </div>

        <div class="server-info">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>🌐 Server URL:</strong>
                <span class="status-badge">● ACTIVE</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <code id="apiUrl" style="color: var(--institutional-orange); font-size: 1.1rem;">{{ $apiUrl }}</code>
                <button class="btn-copy" onclick="copyToClipboard()">
                    📋 Copiar
                </button>
            </div>
        </div>

        <div class="server-info">
            <div class="mb-2"><strong>🖥️ Sistema:</strong> Order QR System</div>
            <div class="mb-2"><strong>📡 Tipo:</strong> {{ $isNgrok ? '🌍 Público (ngrok)' : '🏠 Local Network' }}</div>
            <div class="mb-2"><strong>🌐 Host:</strong> {{ request()->getHost() }}</div>
            @if(!$isNgrok)
                <div><strong>🔌 Puerto:</strong> {{ request()->getPort() }}</div>
            @endif
        </div>

        <div class="instructions">
            <h5>📋 Instrucciones:</h5>
            <ol>
                <li>Abre la aplicación móvil <strong>Order QR</strong></li>
                <li>Ve a <strong>Configuración</strong> o <strong>Settings</strong></li>
                <li>Toca en <strong>"Escanear configuración QR"</strong></li>
                <li>Escanea el código QR mostrado arriba</li>
                <li>La aplicación se configurará automáticamente</li>
            </ol>
            @if($isNgrok)
                <div class="alert alert-success mt-3 mb-0" role="alert">
                    <small><strong>✅ ngrok activo:</strong> Funciona desde cualquier red con conexión a internet. No necesitas estar en la misma WiFi.</small>
                </div>
            @else
                <div class="alert alert-warning mt-3 mb-0" role="alert">
                    <small><strong>⚠️ Importante:</strong> Asegúrate de estar conectado a la misma red WiFi que el servidor</small>
                </div>
            @endif
        </div>

        <div class="text-center mt-4">
            <small class="text-muted">
                Generado automáticamente por <strong>{{ $isNgrok ? 'start-with-ngrok.bat' : 'start-server.bat' }}</strong>
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        const apiUrl = "{{ $apiUrl }}";
        new QRCode(document.getElementById("qrcode"), {
            text: apiUrl,
            width: 256,
            height: 256,
            colorDark: "#1d4976",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Copy to clipboard function
        function copyToClipboard() {
            const url = document.getElementById('apiUrl').textContent;
            navigator.clipboard.writeText(url).then(() => {
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '✓ Copiado!';
                btn.style.background = '#28a745';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '';
                }, 2000);
            });
        }

        // Auto-refresh every 30 seconds to update status
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
