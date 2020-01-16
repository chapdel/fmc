import { listen } from '../util';
import { showModal } from './modal';

listen('submit', '[data-confirm]', ({ event, target }) => {
    event.preventDefault();

    showModal('confirm', {
        onConfirm() {
            target.submit();
        },
    });
});
