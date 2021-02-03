<nav class="card-nav">
    <h4 class="card-nav-title truncate">
        {{ $title ?? '' }}
    </h4>
    <ul>
        {{ $slot }}
    </ul>
</nav>