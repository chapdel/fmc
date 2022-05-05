import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

require('./components/swup');
require('./components/confirm');
require('./components/datepicker');
require('./components/dirty');
require('./components/dismiss');
require('./components/htmlPreview');
require('./components/segments');
require('./components/tags');
require('./components/charts/emailListStatistics');
require('./components/charts/campaignStatistics');

Alpine.plugin(focus);

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('modals', {
        openModals: [],
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
