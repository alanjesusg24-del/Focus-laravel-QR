<!DOCTYPE html>
<html>
<head>
    <title>Test Iconos Font Awesome 5.11.2</title>
    <link type="text/css" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .icon-test { display: inline-block; margin: 10px; padding: 10px; border: 1px solid #ccc; }
        .icon-test i { font-size: 24px; margin-right: 10px; }
        .error { background-color: #fee; }
    </style>
</head>
<body>
    <h1>Test de Iconos - Font Awesome 5.11.2</h1>

    <h2>Iconos Usados en Orders:</h2>

    <div class="icon-test">
        <x-icon name="success" /> success
    </div>

    <div class="icon-test">
        <x-icon name="error" /> error
    </div>

    <div class="icon-test">
        <x-icon name="add" /> add
    </div>

    <div class="icon-test">
        <x-icon name="search" /> search
    </div>

    <div class="icon-test">
        <x-icon name="qrcode" /> qrcode
    </div>

    <div class="icon-test">
        <x-icon name="phone" /> phone
    </div>

    <div class="icon-test">
        <x-icon name="chat" /> chat
    </div>

    <h2>Prueba Directa (sin componente):</h2>

    <div class="icon-test">
        <i class="fa-solid fa-check-circle"></i> fa-check-circle
    </div>

    <div class="icon-test">
        <i class="fa-solid fa-times-circle"></i> fa-times-circle
    </div>

    <div class="icon-test">
        <i class="fa-solid fa-plus"></i> fa-plus
    </div>

    <div class="icon-test">
        <i class="fa-solid fa-search"></i> fa-search
    </div>

    <div class="icon-test">
        <i class="fa-solid fa-qrcode"></i> fa-qrcode
    </div>

    <div class="icon-test">
        <i class="fa-solid fa-phone"></i> fa-phone
    </div>

    <div class="icon-test">
        <i class="fa-solid fa-comments"></i> fa-comments
    </div>

    <h2>Versión de Font Awesome:</h2>
    <p>Instalada: <strong>5.11.2</strong></p>
    <p>Ruta: <code>{{ asset('vendor/fontawesome-free/css/all.min.css') }}</code></p>

    <h2>Clases generadas por componente:</h2>
    <pre>
success: {{ config('icons.icons.success') }}
error: {{ config('icons.icons.error') }}
add: {{ config('icons.icons.add') }}
search: {{ config('icons.icons.search') }}
qrcode: {{ config('icons.icons.qrcode') }}
phone: {{ config('icons.icons.phone') }}
chat: {{ config('icons.icons.chat') }}
    </pre>
</body>
</html>
