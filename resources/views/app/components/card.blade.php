<div class="card {{ isset($nav) ? 'card-split' : '' }}">
        
        {{ $nav ?? '' }}

        <section class="card-main">
            {{ $slot }}
        </section>
    </div>