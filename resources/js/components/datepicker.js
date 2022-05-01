import flatpickr from 'flatpickr';

function initDatepickers() {
    document.querySelectorAll('[data-datepicker]').forEach(node => {
        const minDate = node.dataset.minDate !== undefined
            ? node.dataset.minDate
            : 'today';

        const maxDate = node.dataset.maxDate !== undefined
            ? node.dataset.maxDate
            : null;

        const position = node.dataset.position !== undefined
            ? node.dataset.position
            : 'above';

        flatpickr(node, { dateFormat: 'Y-m-d', minDate: minDate, maxDate: maxDate, position: position });
    });
}

document.addEventListener('turbo:load', initDatepickers);
