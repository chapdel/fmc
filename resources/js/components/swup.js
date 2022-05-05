import Swup from 'swup';
import SwupLivewirePlugin from '@swup/livewire-plugin';
import SwupA11yPlugin from '@swup/a11y-plugin';
import SwupPreloadPlugin from '@swup/preload-plugin';

window.swup = new Swup({
    linkSelector:
        'a[href^="' +
        window.location.origin +
        '"]:not([data-no-swup], [wire\\:click\\.prevent], [wire\\:click], [target]), a[href^="/"]:not([data-no-swup]), a[href^="#"]:not([data-no-swup])',
    animationSelector: '[class*="swup-transition-"]',
    plugins: [
        new SwupLivewirePlugin(),
        new SwupA11yPlugin(),
        new SwupPreloadPlugin(),
    ],
});
