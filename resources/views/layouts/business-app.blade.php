{{--
  Company: CETAM
  Project: FQR
  File: business-app.blade.php
  Created on: 21/11/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno
--}}

@extends('layouts.base')

@section('content')
    @php($routeName = request()->route()?->getName())
    @php($prefix = 'business.')

    @php($isAppShell = str_starts_with($routeName, $prefix))
    @php($isAuthShell = in_array($routeName, [
        'business.login', 'business.register', 'business.forgot-password'
    ]))

    @if($isAppShell && !$isAuthShell)
        {{-- Nav for mobile --}}
        @include('layouts.business-nav')

        {{-- SideNav --}}
        @include('layouts.business-sidenav')

        <main class="content">
            {{-- TopBar --}}
            @include('layouts.business-topbar')

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
        {{-- Alternative Footer --}}
        @include('layouts.footer2')
    @else
        {{-- Fallback: plane content --}}
        @hasSection('page')
            @yield('page')
        @else
            {{ $slot ?? '' }}
        @endif
    @endif
@endsection
