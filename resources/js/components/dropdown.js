import { listen, $, $$, enter, leave } from '../util';

listen('click', '[data-dropdown-trigger]', ({ target }) => {
    const dropdownList = $('[data-dropdown-list]', target.closest('[data-dropdown]'));

    if (!dropdownList.classList.contains('hidden')) {
        return;
    }

    enter(dropdownList, 'fade');
    target.classList.add('dropdown-trigger-open');

    function handleClick(event) {
        if (dropdownList.contains(event.target)) {
            return;
        }

        leave(dropdownList, 'fade');
        target.classList.remove('dropdown-trigger-open');

        window.removeEventListener('click', handleClick);
    }

    setTimeout(() => {
        window.addEventListener('click', handleClick);
    });
});

listen('mouseover', '[data-dropdown-trigger-hover]', ({ target }) => {
    const dropdownList = $('[data-dropdown-list]', target.closest('[data-dropdown]'));

    if (!dropdownList.classList.contains('hidden')) {
        return;
    }

    const dropdownLists = $$('[data-dropdown-list]');
    const dropdownTriggers = $$('[data-dropdown-trigger-hover');

    dropdownLists.forEach((list) => {
        if (list.classList.contains('hidden')) {
            return;
        }

        leave(list, 'fade'); 
    })

    dropdownTriggers.forEach((trigger) => {
        trigger.classList.remove('dropdown-trigger-open');
    })

    enter(dropdownList, 'fade');
    target.classList.add('dropdown-trigger-open');

    function handleClick(event) {
        if (target.contains(event.target)) {
            return;
        }

        leave(dropdownList, 'fade');
        target.classList.remove('dropdown-trigger-open');

        window.removeEventListener('click', handleClick);
    }

    setTimeout(() => {
        window.addEventListener('click', handleClick);
    });
});

listen('mouseover', '[data-dropdown-close-all]', ({ target }) => {
    const dropdownLists = $$('[data-dropdown-list]');
    const dropdownTriggers = $$('[data-dropdown-trigger-hover');

    dropdownLists.forEach((list) => {
        if (list.classList.contains('hidden')) {
            return;
        }

        leave(list, 'fade'); 
    })

    dropdownTriggers.forEach((trigger) => {
        trigger.classList.remove('dropdown-trigger-open');
    })
});