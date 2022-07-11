@props([
    'buttons' => false,
    'class' => '',
])

<div id="card-buttons" class="{{ $buttons? 'card-buttons' : 'card form-grid' }} {{ $class }}" {{ $attributes->except('class') }}>
    {{ $slot }}
</div>

@if($buttons)
<script>
    // get the sticky element
const stickyElm = document.querySelector('#card-buttons')

const observer = new IntersectionObserver( 
  ([e]) => e.target.classList.toggle('card-buttons-stuck', e.intersectionRatio < 1),
  {threshold: [1]}
);

observer.observe(stickyElm)
</script>
@endif
