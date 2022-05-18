document.addEventListener('alpine:init', () => {
    window.Alpine.data('navigation', () => ({
        init() {
            const coords = this.$el.querySelector('.navigation-item').getBoundingClientRect();
            this.$refs.background.style.setProperty('transform', `translate(${coords.left}px, ${coords.top}px`);
        },

        open(event) {
            if (event.target.classList.contains('navigation-link')) {
                return;
            }

            event.preventDefault();

            document.querySelectorAll('.navigation-dropdown').forEach((el) => el.classList.add('hidden', 'opacity-0'));

            const target = event.target.classList.contains('navigation-item')
                ? event.target
                : event.target.closest('.navigation-item');

            const dropdown = target.querySelector('.navigation-dropdown');
            const background = this.$refs.background;

            dropdown.classList.remove('hidden');

            setTimeout(() => {
                if(! dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('opacity-0');
                    dropdown.classList.add('opacity-100');
                }
            }, 150);

            background.classList.remove('opacity-0');
            background.classList.add('opacity-100');

            const dropdownCoords = dropdown.getBoundingClientRect();
            const navCoords = document.querySelector('.navigation-main').getBoundingClientRect();

            const coords = {
                height: dropdownCoords.height,
                width: dropdownCoords.width,
                top: dropdownCoords.top - navCoords.top,
                left: dropdownCoords.left - navCoords.left
            };

            background.style.setProperty('width', `${coords.width}px`);
            background.style.setProperty('height', `${coords.height}px`);
            background.style.setProperty('transform', `translate(${coords.left}px, ${coords.top}px`);
        },

        close(event) {
            document.querySelectorAll('.navigation-dropdown').forEach((el) => {
                el.classList.remove('block', 'opacity-100');
                el.classList.add('hidden', 'opacity-0');
            });

            this.$refs.background.classList.add('opacity-0');
            this.$refs.background.classList.remove('opacity-100');
        }
    }));
});
