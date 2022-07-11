<div id="form-buttons" class="form-buttons {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    {{ $slot }}
</div>

<script>
    // get the sticky element
const stickyElm = document.querySelector('#form-buttons')

const observer = new IntersectionObserver( 
  ([e]) => e.target.classList.toggle('form-buttons-stuck', e.intersectionRatio < 1),
  {threshold: [1]}
);

observer.observe(stickyElm)
</script>
