import { $, listen } from '../util';


listen('submit', '[data-confirm]', ({ event, target }) => {
    event.preventDefault();

    if (target.dataset.confirmText) {
        setModalText(target.dataset.confirmText);
    }

    /*showModal('confirm', {
        onConfirm() {
            window.setTimeout(() => {
                setModalText(__('mailcoach - Are you sure?'));
            }, 150);

            target.submit();
        },
        onDismiss() {
            window.setTimeout(() => {
                setModalText(__('mailcoach - Are you sure?'));
            }, 150);
        },
    });*/
});

function setModalText(text) {
    const modalText = $('[data-confirm-modal-text]');

    if (modalText) {
        modalText.innerText = text;
    }
}
