import Turbolinks from 'turbolinks';
import { $, debounce, listen } from '../util';

Turbolinks.start();

let scrollPosition;
let preserveSearchFocusOn;

listen('click', '[data-turbolinks-preserve-scroll]', () => {
    scrollPosition = window.scrollY;
});

listen(
    'input',
    '[data-turbolinks-search]',
    debounce(({ target }) => {
        const url = target.value
            ? target.dataset.turbolinksSearchUrl.replace('%search%', target.value)
            : target.dataset.turbolinksSearchClearUrl;

        scrollPosition = window.scrollY;
        preserveSearchFocusOn = document.activeElement.matches('[data-turbolinks-search]') ? url : null;

        Turbolinks.visit(url, { action: 'replace' });
    }, 400)
);

document.addEventListener('turbolinks:render', () => {
    if (preserveSearchFocusOn === window.location.href) {
        $('[data-turbolinks-search]').focus();
    }

    if (scrollPosition) {
        window.scrollTo(0, scrollPosition);

        scrollPosition = undefined;
    }
});
