import * as Turbo from "@hotwired/turbo";
import { debounce, listen } from '../util';

// Preserve scroll

let preservedScrollPosition = null;

function preserveScrollPosition() {
    preservedScrollPosition = window.scrollY;
}

function restoreScrollPosition() {
    if (preservedScrollPosition) {
        window.scrollTo(0, preservedScrollPosition);

        preservedScrollPosition = null;
    }
}

listen('click', '[data-turbo-preserve-scroll]', preserveScrollPosition);
document.addEventListener('turbo:render', restoreScrollPosition);

// Preserve focus

let preservedFocus = null;

function preserveFocus() {
    if (document.activeElement) {
        preservedFocus = document.activeElement.matches('[data-turbo-permanent]') ? document.activeElement : null;
    }
}

function restoreFocus() {
    if (preservedFocus) {
        preservedFocus.focus();

        preservedFocus = null;
    }
}

document.addEventListener('turbo:before-visit', preserveFocus);
document.addEventListener('turbo:render', restoreFocus);

// Search bar

listen(
    'input',
    '[data-turbo-search]',
    debounce(({ target }) => {
        const url = target.value
            ? target.dataset.turboSearchUrl.replace('%search%', target.value)
            : target.dataset.turboSearchClearUrl;

        preserveScrollPosition();

        Turbo.visit(url, { action: 'replace' });
    }, 400)
);
