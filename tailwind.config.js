const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/wire-elements/spotlight/resources/views/spotlight.blade.php',
        '../mailcoach-ui/resources/**/*.blade.php',
      ],
    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.gray,
            blue: colors.blue,
            red: colors.rose,
            green: colors.emerald,
            yellow: colors.amber,
            orange: colors.orange,
        },
        extend: {
            fontFamily: {
                sans: [
                    'Inter',
                    ...defaultTheme.fontFamily.sans,
                ]
            },
            boxShadow: {
                focus: '0 2px 2px #e5e3e1' ,
            },
            gridTemplateColumns: {
                auto: 'auto',
                'auto-1fr': 'auto 1fr',
                '1fr-auto': '1fr auto',
            },
            inset: {
                full: '100%',
            },
            minHeight: {
                4: '1rem',
                6: '1.5rem',
                8: '2rem',
                10: '2.5rem',
            },
            minWidth: {
                4: '1rem',
                6: '1.5rem',
                8: '2rem',
                10: '2.5rem',
                32: '8rem',
            },
            maxWidth: {
                layout: '110rem',
            },
            backgroundSize: {
                'size-200': '200% 200%',
            },
            backgroundPosition: {
                'pos-0': '0% 0%',
                'pos-100': '100% 100%',
            },
            keyframes: {
                scale: {
                    '0%, 100%': { transform: 'scale(1)' },
                    '25%': { transform: 'scale(1.05)' },
                    '50%': { transform: 'scale(1)' },
                    '75%': { transform: 'scale(1.05)' },
                }
            },
            animation: {
                scale: 'scale 300ms ease-in-out',
            }
        },
    },
    corePlugins: {
        ringWidth: false,
    }
};
