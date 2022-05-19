document.addEventListener('alpine:init', () => {
    window.Alpine.data('navigation', () => ({
        show: true,

        init() {
            this.$nextTick(() => {
                const coords = this.$el.querySelector('.navigation-dropdown').closest('.navigation-item').getBoundingClientRect();
                console.log(coords);
                this.$refs.background.style.setProperty('transform', `translate(${coords.left}px, ${coords.top}px`);

                if (window.innerWidth < 768) {
                    this.show = false;
                }
            })
        },

        open(event) {
            if (event.target.classList.contains('navigation-link')) {
                return;
            }

            if (window.innerWidth < 768) {
                return;
            }

            event.preventDefault();

            document.querySelectorAll('.navigation-dropdown').forEach((el) => el.classList.add('md:hidden', 'md:opacity-0'));

            const target = event.target.classList.contains('navigation-item')
                ? event.target
                : event.target.closest('.navigation-item');

            const dropdown = target.querySelector('.navigation-dropdown');
            const background = this.$refs.background;

            dropdown.classList.remove('md:hidden');

            setTimeout(() => {
                if(! dropdown.classList.contains('md:hidden')) {
                    dropdown.classList.remove('md:opacity-0');
                    dropdown.classList.add('md:opacity-100');
                }
            }, 150);

            background.classList.remove('md:opacity-0');
            background.classList.add('md:opacity-100');

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
            if (window.innerWidth < 768) {
                return;
            }

            document.querySelectorAll('.navigation-dropdown').forEach((el) => {
                el.classList.remove('md:block', 'md:opacity-100');
                el.classList.add('md:hidden', 'md:opacity-0');
            });

            this.$refs.background.classList.add('md:opacity-0');
            this.$refs.background.classList.remove('md:opacity-100');
        }
    }));
});
