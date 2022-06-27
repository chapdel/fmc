import flatpickr from 'flatpickr';
import Tagify from '@yaireo/tagify'
import '@yaireo/tagify/dist/tagify.css';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Clipboard from "@ryangjchandler/alpine-clipboard";

import {
    Chart,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
} from 'chart.js';
import zoomPlugin from 'chartjs-plugin-zoom';

Chart.register(
    LineElement,
    BarElement,
    PointElement,
    BarController,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
);
import { each } from 'chart.js/helpers';

Chart.register(zoomPlugin);

window.Chart = Chart;
window.Chart.helpers = {};
window.Chart.helpers.each = each;

require('./components/swup');
require('./components/dirty');
require('./components/htmlPreview');
require('./components/charts/emailListStatistics');
require('./components/charts/campaignStatistics');
require('./components/charts/dashboardChart');
require('./components/navigation');

Alpine.plugin(focus);
Alpine.plugin(Clipboard);

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
