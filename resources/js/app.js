import '../css/app.css';
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

import './components/swup.js';
import './components/dirty.js';
import './components/htmlPreview.js';
import './components/charts/emailListStatistics.js';
import './components/charts/campaignStatistics.js';
import './components/charts/dashboardChart.js';
import './components/navigation.js';

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
