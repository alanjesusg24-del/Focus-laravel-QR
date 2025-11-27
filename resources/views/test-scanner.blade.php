<!DOCTYPE html>
<html>
<head>
    <title>Test QR Scanner</title>
    <link type="text/css" href="http://192.168.1.66:8000/vendor/notyf/notyf.min.css" rel="stylesheet">
    <script src="http://192.168.1.66:8000/vendor/notyf/notyf.min.js"></script>
</head>
<body>
    <h1>Test QR Scanner Listener</h1>
    <p>Haz clic aquí y escanea el QR o escribe algo</p>
    <div style="padding: 50px; background: #f0f0f0; margin: 20px;">
        <h2>Área de escaneo</h2>
        <p>Presiona aquí y escanea</p>
    </div>

    <div id="log" style="margin-top: 20px; padding: 10px; background: #000; color: #0f0; font-family: monospace; height: 300px; overflow-y: scroll;">
        <div>Console Log:</div>
    </div>

    <script>
        (function() {
            let scanBuffer = '';
            let scanTimeout = null;
            const SCAN_TIMEOUT = 100;
            const MIN_TOKEN_LENGTH = 10;

            const notyf = new Notyf({
                duration: 5000,
                position: { x: 'right', y: 'top' }
            });

            const logDiv = document.getElementById('log');
            function log(msg) {
                const div = document.createElement('div');
                div.textContent = new Date().toLocaleTimeString() + ': ' + msg;
                logDiv.appendChild(div);
                logDiv.scrollTop = logDiv.scrollHeight;
                console.log(msg);
            }

            document.addEventListener('keydown', function(e) {
                log('[KeyDown] Key: ' + e.key + ', Code: ' + e.code + ', Target: ' + e.target.tagName);

                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    log('[Ignored] Inside input/textarea');
                    return;
                }

                if (e.key.length > 1 && e.key !== 'Enter') {
                    log('[Ignored] Special key');
                    return;
                }

                if (e.key !== 'Enter') {
                    scanBuffer += e.key;
                    log('[Buffer] ' + scanBuffer + ' (Length: ' + scanBuffer.length + ')');
                }

                clearTimeout(scanTimeout);

                if (e.key === 'Enter') {
                    log('[Enter] Processing scan...');
                    e.preventDefault();
                    if (scanBuffer.length > 0) {
                        processScan(scanBuffer);
                    }
                    scanBuffer = '';
                } else {
                    scanTimeout = setTimeout(function() {
                        log('[Timeout] Buffer length: ' + scanBuffer.length);
                        if (scanBuffer.length >= MIN_TOKEN_LENGTH) {
                            processScan(scanBuffer);
                        }
                        scanBuffer = '';
                    }, SCAN_TIMEOUT);
                }
            });

            function processScan(scannedData) {
                log('[Processing] Data: ' + scannedData);
                const trimmedData = scannedData.trim();

                if (trimmedData.length < MIN_TOKEN_LENGTH) {
                    log('[Error] Data too short: ' + trimmedData.length);
                    return;
                }

                let token = extractToken(trimmedData);
                log('[Extracted] Token: ' + token);

                if (!token) {
                    log('[Error] No valid token extracted');
                    notyf.error('No se pudo extraer el token');
                    return;
                }

                notyf.success('Token detectado: ' + token);
                validateDelivery(token);
            }

            function extractToken(data) {
                if (data.includes('/storage/qr_codes/')) {
                    const match = data.match(/order_\d+_([a-zA-Z0-9\-_]+)\.(svg|png)/);
                    if (match && match[1]) {
                        return match[1];
                    }
                    return null;
                }

                if (data.includes('/pickup/')) {
                    const match = data.match(/\/pickup\/([a-zA-Z0-9\-_]+)/);
                    if (match && match[1]) {
                        return match[1];
                    }
                }

                if (/^[a-zA-Z0-9\-_]+$/.test(data)) {
                    return data;
                }

                return null;
            }

            function validateDelivery(pickupToken) {
                log('[API] Sending request...');
                fetch('http://192.168.1.66:8000/api/v1/scanner/validate-delivery', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ pickup_token: pickupToken })
                })
                .then(response => response.json())
                .then(data => {
                    log('[API] Response: ' + JSON.stringify(data));
                    if (data.success) {
                        notyf.success('Orden ' + data.data.folio_number + ' entregada!');
                    } else {
                        notyf.error(data.message);
                    }
                })
                .catch(error => {
                    log('[API] Error: ' + error);
                    notyf.error('Error al procesar');
                });
            }

            log('[Scanner] Initialized - Ready to scan!');
        })();
    </script>
</body>
</html>
