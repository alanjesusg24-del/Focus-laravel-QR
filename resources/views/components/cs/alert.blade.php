{{--
  Company: CETAM
  Project: FQR
  File: footer2.blade.php
  Created on: 02/10/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moren
--}}

<div class="alert {{ $alertClass }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    <x-icon :name="$iconName" class="me-2" />
    {{ $message }}

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
