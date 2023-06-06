module.exports = {
    'resources/{css,js}/**/*.{css,js}': ['prettier --write', 'git add'],
    'resources/**/*.{css,js}': () => ['npm run build', 'git add resources/dist'],
    '**/*.php': ['composer format'],
};
