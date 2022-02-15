{{-- [data-turbo-permanent] is necessary to preserve the cursor state during focus --}}
<div class="search">
    <input type="search" required placeholder="{{ $placeholder }}" value="{{ $value }}"
        autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
        id="turbo-search-{{ Illuminate\Support\Str::slug($queryString->disable('filter[search]')) }}"
        data-turbo-permanent
        data-turbo-search
        data-turbo-search-url="{{ url($queryString->filter('search', '%search%')) }}"
        data-turbo-search-clear-url="{{ url($queryString->disable('filter[search]')) }}">
    <div class="search-icon">
        <i class="fas fa-search"></i>
    </div>
</div>
