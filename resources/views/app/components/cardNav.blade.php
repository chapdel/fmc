<nav class="card-nav">
    <h4 class="text-blue-200 text-opacity-50 flex justify-end font-bold text-xs uppercase tracking-widest mb-6">
        {{ $title ?? '' }}
    </h4>
    <ul>
        {{ $slot }}
    </ul>
</nav>