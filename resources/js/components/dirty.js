import * as Turbo from '@hotwired/turbo';
import { listen, $ } from '../util';

listen('input', '[data-dirty-check]', ({ target }) => {
    target.dirty = true;
});

listen('click', '[data-dirty-warn]', () => {
    if (!$('[data-dirty-check]') || !$('[data-dirty-check]').dirty) {
        return;
    }

    function handleBeforeVisit(event) {
        event.preventDefault();

        /*showModal('dirty-warning', {
            onConfirm() {
                Turbo.visit(event.data.url);
            },
        });*/
    }

    document.addEventListener('turbo:before-visit', handleBeforeVisit, { once: true });
});
