import flatpickr from 'flatpickr';
import Tagify from '@yaireo/tagify'
import '@yaireo/tagify/dist/tagify.css';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

require('./components/swup');
require('./components/dirty');
require('./components/htmlPreview');
require('./components/charts/emailListStatistics');
require('./components/charts/campaignStatistics');
require('./components/navigation');

Alpine.plugin(focus);

window.Alpine = Alpine;
window.Tagify = Tagify;

document.addEventListener('alpine:init', () => {
    Alpine.store('modals', {
        openModals: [],
        onConfirm: null,
        isOpen(id) {
            return this.openModals.includes(id);
        },
        open(id) {
            this.openModals.push(id);
        },
        close(id) {
            this.openModals = this.openModals.filter((modal) => modal !== id);
        }
    });
});

Alpine.start();
