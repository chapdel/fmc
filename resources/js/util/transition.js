export function enter(element, transitionName = 'transition') {
    return new Promise(resolve => {
        element.classList.remove('hidden');

        element.classList.add(`${transitionName}-enter`);
        element.classList.add(`${transitionName}-enter-active`);

        nextFrame(() => {
            element.classList.remove(`${transitionName}-enter`);

            afterTransition(element, () => {
                element.classList.remove(`${transitionName}-enter-active`);

                nextFrame(() => {
                    resolve(element);
                });
            });
        });
    });
}

export function leave(element, transitionName = 'transition') {
    return new Promise(resolve => {
        element.classList.add(`${transitionName}-leave-active`);

        nextFrame(() => {
            element.classList.add(`${transitionName}-leave`);

            afterTransition(element, () => {
                element.classList.remove(`${transitionName}-leave-active`);
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
