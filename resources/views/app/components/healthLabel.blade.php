<span class="inline-flex md:flex-row-reverse gap-2 items-center">
    <x-mailcoach::rounded-icon :type="$test ? 'success' : (isset($warning) && $warning ? 'warning' : 'error')" :icon="$test ? 'fa-fw fas fa-check' : (isset($warn) && $warn ? 'fas fa-exclamation' : 'fas fa-times')"/>
    @if(isset($label))
    <span>
        {{ $label }}
    </span>
    @endisset
</span>
