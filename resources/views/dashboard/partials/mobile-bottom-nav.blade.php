@php
    $mnav = isset($mnavActive) ? $mnavActive : 'dashboard';
@endphp
<nav class="db-mnav d-md-none" role="navigation" aria-label="Quick navigation">
    <a href="{{ route('user.dashboard') }}"
       class="db-mnav__link {{ $mnav === 'dashboard' ? 'is-active' : '' }}">
        <i class="fas fa-house" aria-hidden="true"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('user.dashboard') }}#transactions"
       class="db-mnav__link {{ $mnav === 'transactions' ? 'is-active' : '' }}">
        <i class="fas fa-receipt" aria-hidden="true"></i>
        <span>List</span>
    </a>
    <a href="{{ route('user.dashboard.create') }}"
       class="db-mnav__link db-mnav__link--create {{ $mnav === 'create' ? 'is-active' : '' }}">
        <span class="db-mnav__fab" aria-hidden="true">
            <i class="fas fa-plus"></i>
        </span>
        <span>Create</span>
    </a>
    <a href="{{ route('user.dashboard') }}#profile"
       class="db-mnav__link {{ $mnav === 'profile' ? 'is-active' : '' }}">
        <i class="fas fa-user" aria-hidden="true"></i>
        <span>Profile</span>
    </a>
</nav>
