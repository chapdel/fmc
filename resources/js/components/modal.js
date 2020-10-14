import { $, $$, listen, noop, trapFocus, enter, leave } from '../util';

export function showModal(name, { onConfirm = noop, onDismiss = noop } = {}) {
    const modal = $(`[data-modal="${name}"]`);

    enter(modal, 'fade');

    const releaseFocus = trapFocus(modal);

    bindCloseListeners(modal, { onConfirm, onDismiss, onClose: releaseFocus });

    updateUrl(true);
}

function updateUrl(open) {
    const url = new URL(window.location.href);
    url.searchParams[open ? 'append' : 'delete']('modal', true);
    window.history.pushState({}, '', url.href);
}

function bindCloseListeners(modal, { onConfirm, onDismiss, onClose }) {
    function handleEscape(event) {
        if (event.key === 'Escape') {
            modal.dispatchEvent(new Event('dismiss'));
        }
    }

    function handleConfirm() {
        onConfirm();
        handleClose();
        leave(modal, 'fade');
    }

    function handleDismiss() {
        onDismiss();
        handleClose();
        leave(modal, 'fade');
    }

    function handleClose() {
        onClose();
        window.removeEventListener('keydown', handleEscape);
        modal.removeEventListener('confirm', handleConfirm);
        modal.removeEventListener('dismiss', handleDismiss);

        updateUrl(false);
    }

    window.addEventListener('keydown', handleEscape);
    modal.addEventListener('confirm', handleConfirm);
    modal.addEventListener('dismiss', handleDismiss);
}

listen('click', '[data-modal-trigger]', ({ target }) => {
    showModal(target.dataset.modalTrigger);
});

listen('click', '[data-modal-confirm]', ({ target }) => {
    const modal = target.closest('[data-modal]');

    modal.dispatchEvent(new Event('confirm'));
});

listen('click', '[data-modal-dismiss]', ({ target }) => {
    const modal = target.closest('[data-modal]');

    modal.dispatchEvent(new Event('dismiss'));
});

listen('click', '[data-modal-backdrop]', ({ event, target }) => {
    // The entire modal is nested in the backdrop. This check avoids closing
    // the modal on any click in the modal.
    if (event.target === target) {
        const modal = target.closest('[data-modal]');

        modal.dispatchEvent(new Event('dismiss'));
    }
});

document.addEventListener('turbolinks:load', () => {
    $$('[data-modal]')
        .filter(modal => !modal.classList.contains('hidden'))
        .forEach(modal => {
            bindCloseListeners(modal, {
                onConfirm: noop,
                onDismiss: noop,
                onClose: noop,
            });
        });
});
