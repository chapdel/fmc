<div class="card {{ $nav ? 'card-split' : '' }}">
        
        {{ $nav }}

        <section class="card-main">
            {{ $slot }}
        </section>
    </div>