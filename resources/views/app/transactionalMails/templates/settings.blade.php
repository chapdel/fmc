<x-mailcoach::layout-transactional-template title="Settings" :template="$template">
        <form
            class="form-grid"
            method="POST"
        >
            @csrf
            @method('PUT')

           settings come here
        </form>
    </section>
</x-mailcoach::layout-transactional-template>

