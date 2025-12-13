{{--
  Company: CETAM
  Project: FQR
  File: base.blade.php
  Created on: 21/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno

  Changelog:
  - ID: 1 | Date: 24/11/2025 | 
    Modified by: Dafne Vanessa Castillo Moreno | 
    Description: Change base layout to include QR scanner global listener
--}}

<!DOCTYPE html>
<html lang="es">

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

    {{-- Favicons --}}
    <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}" sizes="16x16" type="image/png">

    <link rel="mask-icon" href="{{ asset('assets/img/favicon/safari-pinned-tab.svg') }}" color="#563d7c">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <meta name="msapplication-config" content="{{ asset('assets/img/favicons/browserconfig.xml') }}">
    <meta name="theme-color" content="#563d7c">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Apex Charts --}}
    <link type="text/css" href="{{ asset('vendor/apexcharts/apexcharts.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Datepicker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.4/dist/css/datepicker.min.css">

    {{-- Fontawesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Sweet Alert 2 --}}
    <link type="text/css" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    {{-- Notyf --}}
    <link type="text/css" href="{{ asset('vendor/notyf/notyf.min.css') }}" rel="stylesheet">

    {{-- Volt CSS --}}
    <link type="text/css" href="{{ asset('css/volt.css') }}?v={{ filemtime(public_path('css/volt.css')) }}" rel="stylesheet">

    {{-- CETAM Institutional Colors --}}
    <link type="text/css" href="{{ asset('css/cetam-colors.css') }}?v={{ filemtime(public_path('css/cetam-colors.css')) }}" rel="stylesheet">

    {{-- CETAM Sidebar Styles --}}
    <link type="text/css" href="{{ asset('css/cetam-sidebar.css') }}" rel="stylesheet">

    @livewireStyles
    @livewireScripts

    {{-- Core --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Vendor JS --}}
    <script src="{{ asset('assets/js/on-screen.umd.min.js') }}"></script>

    {{-- Slider --}}
    <script src="{{ asset('assets/js/nouislider.min.js') }}"></script>

    {{-- Smooth scroll --}}
    <script src="{{ asset('assets/js/smooth-scroll.polyfills.min.js') }}"></script>

    {{-- Apex Charts --}}
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>

    {{-- Charts --}}
    <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/js/chartist-plugin-tooltip.min.js') }}"></script>

    {{-- Datepicker --}}
    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.4/dist/js/datepicker.min.js"></script>

    {{-- Sweet Alerts 2 --}}
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>

    {{-- Session Messages Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtain primary color from CSS variable
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#262B40';

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: {!! json_encode(session('success')) !!},
                    confirmButtonColor: primaryColor,
                    confirmButtonText: 'Aceptar'
                });
            @elseif(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: {!! json_encode(session('error')) !!},
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                });
            @elseif(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: {!! json_encode(session('warning')) !!},
                    confirmButtonColor: '#f0ad4e',
                    confirmButtonText: 'Aceptar'
                });
            @elseif(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: {!! json_encode(session('info')) !!},
                    confirmButtonColor: primaryColor,
                    confirmButtonText: 'Aceptar'
                });
            @endif
        });
    </script>

    {{-- Moment JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

    {{-- Notyf --}}
    <script src="{{ asset('vendor/notyf/notyf.min.js') }}"></script>

    {{-- Simplebar --}}
    <script src="{{ asset('assets/js/simplebar.min.js') }}"></script>

    {{-- Github buttons --}}
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    {{-- Volt JS --}}
    <script src="{{ asset('assets/js/volt.js') }}"></script>

    @if(env('IS_DEMO'))
        {{-- Global site tag (gtag.js) - Google Analytics --}}
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-141734189-6"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-141734189-6');
        </script>
        {{-- Google Tag Manager --}}
        <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
            'gtm.start':
                new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-THQTXJ7');</script>
        {{-- End Google Tag Manager --}}
    @endif

</head>

<body>
    @if(env('IS_DEMO')) 
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-THQTXJ7" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    @hasSection('content')
        @yield('content')
    @else
        {{ $slot }}
    @endif

    @yield('scripts')
    @stack('scripts')

    {{-- QR Scanner Global Listener --}}
    <script>
        (function() {
            // Variables to manage scan state
            let scanBuffer = '';
            let scanTimeout = null;
            const SCAN_TIMEOUT = 100; 
            const MIN_TOKEN_LENGTH = 10; 

            // Initialize Notyf for notifications
            const notyf = new Notyf({
                duration: 5000,
                position: {
                    x: 'right',
                    y: 'top',
                }
            });

            // Global keyboard listener - using keydown for compatibility with more scanners
            document.addEventListener('keydown', function(e) {
                console.log('[QR Scanner] KeyDown detected:', e.key, 'Code:', e.code, 'Target:', e.target.tagName);

                // Ignore if inside an input/textarea
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    console.log('[QR Scanner] Ignored - Inside input/textarea');
                    return;
                }

                // Ignore special keys (except Enter)
                if (e.key.length > 1 && e.key !== 'Enter') {
                    console.log('[QR Scanner] Ignored - Special key');
                    return;
                }

                // Accumulate characters
                if (e.key !== 'Enter') {
                    scanBuffer += e.key;
                    console.log('[QR Scanner] Buffer:', scanBuffer, 'Length:', scanBuffer.length);
                }

                // Reset timeout
                clearTimeout(scanTimeout);

                // Detect end of scan (Enter or timeout)
                if (e.key === 'Enter') {
                    console.log('[QR Scanner] Enter detected - Processing scan');
                    e.preventDefault(); // Prevent form submit
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

            // Process scanned data
            function processScan(scannedData) {
                console.log('[QR Scanner] Processing scan:', scannedData);
                const trimmedData = scannedData.trim();

                if (trimmedData.length < MIN_TOKEN_LENGTH) {
                    console.log('[QR Scanner] Data too short:', trimmedData.length, '- Need at least:', MIN_TOKEN_LENGTH);
                    return;
                }

                // Extract token from scanned data
                let pickupToken = extractToken(trimmedData);
                console.log('[QR Scanner] Extracted token:', pickupToken);

                if (!pickupToken) {
                    console.log('[QR Scanner] No valid token extracted');
                    return;
                }

                // Show processing notification
                notyf.success('Procesando código QR...');

                // Sending to server for validation
                validateDelivery(pickupToken);
            }

            // Extract token from scanned data (can be URL or direct token)
            function extractToken(data) {
                // If it's a URL containing /storage/qr_codes/
                if (data.includes('/storage/qr_codes/')) {
                    // Extract the token from the file name
                    // Format: /storage/qr_codes/{business_id}/order_{order_id}_{TOKEN}.svg
                    const match = data.match(/order_\d+_([a-zA-Z0-9\-_]+)\.(svg|png)/);
                    if (match && match[1]) {
                        return match[1];
                    }
                    return null;
                }

                // If it contains "pickup/" or similar
                if (data.includes('/pickup/')) {
                    const match = data.match(/\/pickup\/([a-zA-Z0-9\-_]+)/);
                    if (match && match[1]) {
                        return match[1];
                    }
                }

                // Validate basic format of direct token (letters, numbers, dashes)
                if (/^[a-zA-Z0-9\-_]+$/.test(data)) {
                    return data;
                }

                return null;
            }

            // Validate delivery on the server
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
                        // Success notification
                        notyf.success('Orden ' + data.data.folio_number + ' entregada exitosamente');

                        // Play success sound
                        playSuccessSound();

                        // Reload the page if we are on the orders view
                        if (window.location.href.includes('/orders')) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    } else {
                        // Error notification
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

            // Play success sound
            function playSuccessSound() {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi2F0fPHcSYELITO89qINwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAo=');
                audio.play().catch(() => {}); 
            }

            // Play error sound
            function playErrorSound() {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA=');
                audio.play().catch(() => {}); 
            }

            console.log('✓ QR Scanner listener initialized - Ready to scan');
        })();
    </script>
</body>

</html>