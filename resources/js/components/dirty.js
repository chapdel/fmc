import { listen, $ } from '../util';

listen('input', '[data-dirty-check]', ({ target }) => {
    target.dirty = true;
});

Livewire.on('notify', function(params) {
    const [message, level] = params;

    if (level === 'success' && $('[data-dirty-check]')) {
        $('[data-dirty-check]').dirty = false;
    }
});

document.addEventListener(
    'click',
    event => {
        if (!event.target.closest(swup.options.linkSelector)) {
            return;
        }

        if (event.target.dataset.dirtyWarn === undefined) {
            return;
        }

        if (!$('[data-dirty-check]') || !$('[data-dirty-check]').dirty) {
            return;
        }

        event.stopImmediatePropagation();
        event.preventDefault();

        Alpine.store('modals').open('dirty-warning');
        Alpine.store('modals').onConfirm = () => {
            Alpine.store('modals').close('dirty-warning');
            swup.loadPage({
                url: event.target.href,
            });
        };
    },
    true
);
