<ul class="navigation">
    @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')
    <c-navigation-item :href="route('mailcoach.campaigns')">
        <span class="icon-label">
            <i class="fas fa-envelope-open"></i>
            <span class="icon-label-text">Campaigns</span>
        </span>
    </c-navigation-item>

    <c-navigation-item :href="route('mailcoach.emailLists')">
        <span class="icon-label">
            <i class="fas fa-address-book"></i>
            <span class="icon-label-text">Lists</span>
        </span>
    </c-navigation-item>
    <c-navigation-item :href="route('mailcoach.templates')">
        <span class="icon-label">
            <i class="fas fa-clipboard"></i>
            <span class="icon-label-text">Templates</span>
        </span>
    </c-navigation-item>
    @include('mailcoach::app.layouts.partials.afterLastMenuItem')
</ul>
