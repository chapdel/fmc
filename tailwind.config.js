const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
    important: true,
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
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
        },
    },
    corePlugins: {
        ringWidth: false,
    }
};
