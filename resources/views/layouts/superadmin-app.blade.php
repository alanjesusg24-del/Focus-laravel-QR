@extends('layouts.base')

@section('content')
    @php($routeName = request()->route()?->getName())
    @php($prefix = 'superadmin.')

    @php($isAppShell = str_starts_with($routeName, $prefix))
    @php($isAuthShell = in_array($routeName, [
        'superadmin.login'
    ]))

    @if($isAppShell && !$isAuthShell)
        {{-- Nav for mobile --}}
        @include('layouts.superadmin-nav')

        {{-- SideNav --}}
        @include('layouts.superadmin-sidenav')

        <main class="content">
            {{-- TopBar --}}
            @include('layouts.superadmin-topbar')

            @hasSection('page')
                @yield('page')
            @else
                {{ $slot ?? '' }}
            @endif

            {{-- Footer --}}
            @include('layouts.footer')
        </main>
    @elseif($isAuthShell)
        @hasSection('page')
            @yield('page')
        @else
            {{ $slot ?? '' }}
        @endif
        {{-- Footer alternativo --}}
        @include('layouts.footer2')
    @else
        {{-- Fallback: contenido plano --}}
        @hasSection('page')
            @yield('page')
        @else
            {{ $slot ?? '' }}
        @endif
    @endif
@endsection
