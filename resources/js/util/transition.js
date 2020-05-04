export function enter(element, transitionName = 'transition') {
    return new Promise(resolve => {
        element.classList.remove('hidden');

        element.classList.add(`${transitionName}-enter`);
        element.classList.add(`${transitionName}-enter-start`);

        nextFrame(() => {
            element.classList.remove(`${transitionName}-enter-start`);
            element.classList.add(`${transitionName}-enter-end`);

            afterTransition(element, () => {
                element.classList.remove(`${transitionName}-enter-end`);
                element.classList.remove(`${transitionName}-enter`);

                nextFrame(() => {
                    resolve(element);
                });
            });
        });
    });
}

export function leave(element, transitionName = 'transition') {
    return new Promise(resolve => {
        element.classList.add(`${transitionName}-leave`);
        element.classList.add(`${transitionName}-leave-start`);

        nextFrame(() => {
            element.classList.remove(`${transitionName}-leave-start`);
            element.classList.add(`${transitionName}-leave-end`);

            afterTransition(element, () => {
                element.classList.remove(`${transitionName}-leave-end`);
                element.classList.remove(`${transitionName}-leave`);

                element.classList.add('hidden');

                nextFrame(() => {
                    resolve(element);
                });
            });
        });
    });
}

function afterTransition(element, callback) {
    const duration = Number(getComputedStyle(element).transitionDuration.replace('s', '')) * 1000;

    setTimeout(callback, duration);
}

function nextFrame(callback) {
    requestAnimationFrame(() => requestAnimationFrame(callback));
}
