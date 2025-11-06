 
<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', config('app.name'))</title>
    @if(env('IS_DEMO')) 
        <link rel="canonical" href="https://themesberg.com/product/laravel/volt-admin-dashboard-template">
        <meta  name="keywords" content="themesberg, updivision, html dashboard, laravel, livewire, laravel livewire, alpine.js, html css dashboard laravel, Volt Laravel Admin Dashboard, livewire volt dashboard, volt admin, livewire dashboard, livewire admin, web dashboard, bootstrap 5 dashboard laravel, bootstrap 5, css3 dashboard, bootstrap 5 admin laravel, volt dashboard bootstrap 5 laravel, frontend, responsive bootstrap 5 dashboard, volt dashboard, volt laravel bootstrap 5 dashboard"></meta>
        <meta  name="description" content="Volt Laravel Admin Dashboard features dozens of UI components and a Laravel backend with Livewire & Alpine.js"></meta>
        <meta  itemprop="name" content="Volt Laravel Admin Dashboard by Themesberg & UPDIVISION"></meta>
        <meta  itemprop="description" content="Volt Laravel Admin Dashboard features dozens of UI components and a Laravel backend with Livewire & Alpine.js"></meta>
        <meta  itemprop="image" content="https://themesberg.s3.us-east-2.amazonaws.com/public/products/volt-laravel-dashboard/volt-free-laravel-dashboard.jpg"></meta>
        <meta  name="twitter:card" content="product"></meta>
        <meta  name="twitter:site" content="@themesberg"></meta>
        <meta  name="twitter:title" content="Volt Laravel Admin Dashboard by Themesberg & UPDIVISION"></meta>
        <meta  name="twitter:description" content="Volt Laravel Admin Dashboard features dozens of UI components and a Laravel backend with Livewire & Alpine.js"></meta>
        <meta  name="twitter:creator" content="@themesberg"></meta>
        <meta  name="twitter:image" content="https://themesberg.s3.us-east-2.amazonaws.com/public/products/volt-laravel-dashboard/volt-free-laravel-dashboard.jpg"></meta>
        <meta  property="fb:app_id" content="655968634437471"></meta>
        <meta  property="og:title" content="Volt Laravel Admin Dashboard by Themesberg & UPDIVISION"></meta>
        <meta  property="og:type" content="article"></meta>
        <meta  property="og:url" content="https://themesberg.com/product/laravel/volt-admin-dashboard-template/preview"></meta>
        <meta  property="og:image" content="https://themesberg.s3.us-east-2.amazonaws.com/public/products/volt-laravel-dashboard/volt-free-laravel-dashboard.jpg"></meta>
        <meta  property="og:description" content="Volt Laravel Admin Dashboard features dozens of UI components and a Laravel backend with Livewire & Alpine.js"></meta>
        <meta  property="og:site_name" content="Themesberg"></meta>
    @endif

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}" sizes="16x16" type="image/png">

    <link rel="mask-icon" href="{{ asset('assets/img/favicon/safari-pinned-tab.svg') }}" color="#563d7c">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <meta name="msapplication-config" content="{{ asset('assets/img/favicons/browserconfig.xml') }}">
    <meta name="theme-color" content="#563d7c">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    
    <!-- Apex Charts -->
    <link type="text/css" href="{{ asset('vendor/apexcharts/apexcharts.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.4/dist/css/datepicker.min.css">

    <!-- Fontawesome -->
    <link type="text/css" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Sweet Alert -->
    <link type="text/css" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Notyf -->
    <link type="text/css" href="{{ asset('vendor/notyf/notyf.min.css') }}" rel="stylesheet">

    <!-- Volt CSS -->
    <link type="text/css" href="{{ asset('css/volt.css') }}" rel="stylesheet">

    @livewireStyles
    @livewireScripts

    <!-- Core -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('assets/js/on-screen.umd.min.js') }}"></script>

    <!-- Slider -->
    <script src="{{ asset('assets/js/nouislider.min.js') }}"></script>

    <!-- Smooth scroll -->
    <script src="{{ asset('assets/js/smooth-scroll.polyfills.min.js') }}"></script>

    <!-- Apex Charts -->
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Charts -->
    <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/js/chartist-plugin-tooltip.min.js') }}"></script>

    <!-- Datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.4/dist/js/datepicker.min.js"></script>

    <!-- Sweet Alerts 2 -->
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>

    <!-- Moment JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

    <!-- Notyf -->
    <script src="{{ asset('vendor/notyf/notyf.min.js') }}"></script>

    <!-- Simplebar -->
    <script src="{{ asset('assets/js/simplebar.min.js') }}"></script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- Volt JS -->
    <script src="{{ asset('assets/js/volt.js') }}"></script>

    @if(env('IS_DEMO'))
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-141734189-6"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-141734189-6');
        </script>
        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
            'gtm.start':
                new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-THQTXJ7');</script>
        <!-- End Google Tag Manager -->
    @endif

</head>

<body>
    @if(env('IS_DEMO')) 
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-THQTXJ7" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @endif
    {{--
        Nota: Este layout soporta tanto slots de componentes Blade como secciones.
        Para migración progresiva: si existe la sección 'content', se usa; de lo contrario, se usa $slot.
        Opcional prefijo de componentes Blade: <x-proj-layouts.base> (sustituir 'proj' por el código real del proyecto).
    --}}

    @hasSection('content')
        @yield('content')
    @else
        {{ $slot }}
    @endif

    @yield('scripts')

    <!-- QR Scanner Global Listener -->
    <script>
        (function() {
            // Variables para detectar el escaneo
            let scanBuffer = '';
            let scanTimeout = null;
            const SCAN_TIMEOUT = 100; // ms entre caracteres del scanner
            const MIN_TOKEN_LENGTH = 10; // longitud mínima del token

            // Inicializar Notyf para notificaciones
            const notyf = new Notyf({
                duration: 5000,
                position: {
                    x: 'right',
                    y: 'top',
                }
            });

            // Listener global de teclado - usando keydown para compatibilidad con más escáneres
            document.addEventListener('keydown', function(e) {
                console.log('[QR Scanner] KeyDown detected:', e.key, 'Code:', e.code, 'Target:', e.target.tagName);

                // Ignorar si está en un input/textarea
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    console.log('[QR Scanner] Ignored - Inside input/textarea');
                    return;
                }

                // Ignorar teclas especiales (excepto Enter)
                if (e.key.length > 1 && e.key !== 'Enter') {
                    console.log('[QR Scanner] Ignored - Special key');
                    return;
                }

                // Acumular caracteres
                if (e.key !== 'Enter') {
                    scanBuffer += e.key;
                    console.log('[QR Scanner] Buffer:', scanBuffer, 'Length:', scanBuffer.length);
                }

                // Resetear timeout
                clearTimeout(scanTimeout);

                // Detectar fin de escaneo (Enter o timeout)
                if (e.key === 'Enter') {
                    console.log('[QR Scanner] Enter detected - Processing scan');
                    e.preventDefault(); // Prevenir submit de forms
                    if (scanBuffer.length > 0) {
                        processScan(scanBuffer);
                    }
                    scanBuffer = '';
                } else {
                    scanTimeout = setTimeout(function() {
                        console.log('[QR Scanner] Timeout - Buffer length:', scanBuffer.length);
                        if (scanBuffer.length >= MIN_TOKEN_LENGTH) {
                            processScan(scanBuffer);
                        }
                        scanBuffer = '';
                    }, SCAN_TIMEOUT);
                }
            });

            // Procesar el código escaneado
            function processScan(scannedData) {
                console.log('[QR Scanner] Processing scan:', scannedData);
                const trimmedData = scannedData.trim();

                if (trimmedData.length < MIN_TOKEN_LENGTH) {
                    console.log('[QR Scanner] Data too short:', trimmedData.length, '- Need at least:', MIN_TOKEN_LENGTH);
                    return;
                }

                // Extraer el token del dato escaneado
                let pickupToken = extractToken(trimmedData);
                console.log('[QR Scanner] Extracted token:', pickupToken);

                if (!pickupToken) {
                    console.log('[QR Scanner] No valid token extracted');
                    return;
                }

                // Mostrar notificación de procesando
                notyf.success('Procesando código QR...');

                // Enviar al servidor
                validateDelivery(pickupToken);
            }

            // Extraer token del dato escaneado (puede ser URL o token directo)
            function extractToken(data) {
                // Si es una URL que contiene /storage/qr_codes/
                if (data.includes('/storage/qr_codes/')) {
                    // Extraer el token del nombre del archivo
                    // Formato: /storage/qr_codes/{business_id}/order_{order_id}_{TOKEN}.svg
                    const match = data.match(/order_\d+_([a-zA-Z0-9\-_]+)\.(svg|png)/);
                    if (match && match[1]) {
                        return match[1]; // Este es el qr_token, no el pickup_token
                    }
                    return null;
                }

                // Si contiene "pickup/" o similar
                if (data.includes('/pickup/')) {
                    const match = data.match(/\/pickup\/([a-zA-Z0-9\-_]+)/);
                    if (match && match[1]) {
                        return match[1];
                    }
                }

                // Validar formato básico del token directo (letras, números, guiones)
                if (/^[a-zA-Z0-9\-_]+$/.test(data)) {
                    return data;
                }

                return null;
            }

            // Validar entrega en el servidor
            function validateDelivery(pickupToken) {
                fetch('{{ url("/api/v1/scanner/validate-delivery") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pickup_token: pickupToken
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Notificación de éxito
                        notyf.success('Orden ' + data.data.folio_number + ' entregada exitosamente');

                        // Reproducir sonido de éxito (opcional)
                        playSuccessSound();

                        // Recargar la página si estamos en la vista de órdenes
                        if (window.location.href.includes('/orders')) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    } else {
                        // Notificación de error
                        notyf.error(data.message);
                        playErrorSound();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    notyf.error('Error al procesar el código QR');
                    playErrorSound();
                });
            }

            // Reproducir sonido de éxito
            function playSuccessSound() {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAo=');
                audio.play().catch(() => {}); // Ignorar errores de audio
            }

            // Reproducir sonido de error
            function playErrorSound() {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA=');
                audio.play().catch(() => {}); // Ignorar errores de audio
            }

            console.log('✓ QR Scanner listener initialized - Ready to scan');
        })();
    </script>
</body>

</html>