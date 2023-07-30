import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import flatpickr from 'flatpickr';
import Clipboard from '@ryangjchandler/alpine-clipboard';
import Choices from 'choices.js';

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
    Legend,
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
    Legend
);
import { each } from 'chart.js/helpers';

Chart.register(zoomPlugin);

window.Chart = Chart;
window.Chart.helpers = {};
window.Chart.helpers.each = each;
window.Choices = Choices;

//import '../../vendor/wire-elements/spotlight/resources/js/spotlight.js';
import './components/dirty.js';
import './components/htmlPreview.js';
import './components/charts/emailListStatistics.js';
import './components/charts/campaignStatistics.js';
import './components/charts/dashboardChart.js';
import './components/navigation.js';
import './components/modals.js';

Alpine.plugin(Clipboard);

Livewire.start();
