import Swup from 'swup';
import SwupLivewirePlugin from '@swup/livewire-plugin';
import SwupA11yPlugin from '@swup/a11y-plugin';
import SwupPreloadPlugin from '@swup/preload-plugin';

new Swup({
    animationSelector: '[class*="swup-transition-"]',
    plugins: [
        new SwupLivewirePlugin(),
        new SwupA11yPlugin(),
        new SwupPreloadPlugin(),
    ],
});
