 {{--
  Company: CETAM
  Project: FQR
  File: app.blade.php
  Created on:14/07/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}

@extends('layouts.base')

@section('content')
    @php($routeName = request()->route()?->getName())
    @php($isAppShell = in_array($routeName, [
        // Main app routes with full navigation 
        'proj.dashboard.index', 'proj.profile.index', 'proj.profile.example', 'proj.users.index',
        'proj.ui.bootstrap-tables', 'proj.billing.transactions', 'proj.ui.buttons', 'proj.ui.forms',
        'proj.ui.modals', 'proj.ui.notifications', 'proj.ui.typography', 'proj.marketing.upgrade-to-pro',
    ]))
    @php($isAuthShell = in_array($routeName, [
        // Authentication/landing paths
        'proj.auth.register', 'proj.examples.register', 'proj.auth.login', 'proj.examples.login',
        'proj.auth.forgot-password', 'proj.examples.forgot-password', 'proj.auth.reset-password', 'proj.examples.reset-password',
    ]))
    @php($isMinimalShell = in_array($routeName, [
        // Minimalist pages
        'proj.errors.404', 'proj.errors.500', 'proj.auth.lock',
    ]))

    @if($isAppShell)
        {{-- Nav --}}
        @include('layouts.nav')
        {{-- SideNav --}}
        @include('layouts.sidenav')
        <main class="content">
            {{-- TopBar --}}
            @include('layouts.topbar')
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
    @elseif($isMinimalShell)
        @hasSection('page')
            @yield('page')
        @else
            {{ $slot ?? '' }}
        @endif
    @else
        {{-- Fallback: plain content --}}
        @hasSection('page')
            @yield('page')
        @else
            {{ $slot ?? '' }}
        @endif
    @endif
@endsection