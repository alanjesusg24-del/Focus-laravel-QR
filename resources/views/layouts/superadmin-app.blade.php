{{--
  Company: CETAM
  Project: FQR
  File: register.blade.php
  Created on: 21/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}

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
        {{-- Alternate Footer --}}
        @include('layouts.footer2')
    @else
        {{-- Fallback: plain content --}}
        @hasSection('page')
            @yield('page')
        @else
            {{ $slot ?? '' }}
        @endif
    @endif
@endsection
