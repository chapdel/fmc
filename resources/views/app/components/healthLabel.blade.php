<span class="inline-flex gap-2 items-center">
    @if(isset($label))
    <span>
        {{ $label }}
    </span>
    @endisset
    <x-mailcoach::rounded-icon :type="$test ? 'success' : (isset($warning) && $warning ? 'warning' : 'error')" :icon="$test ? 'fa-fw fas fa-check' : (isset($warn) && $warn ? 'fas fa-exclamation' : 'fas fa-times')"/>
</span>
