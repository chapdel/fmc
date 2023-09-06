import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard';

import '../../vendor/wire-elements/spotlight/resources/js/spotlight.js';
import './components/choices.js';
import './components/coloris.js';
import './components/dirty.js';
import './components/htmlPreview.js';
import './components/charts';
import './components/navigation.js';
import './components/modals.js';

Alpine.plugin(Clipboard);

Livewire.start();
