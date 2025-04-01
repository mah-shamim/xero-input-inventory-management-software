module.exports = {
    theme: {
        extend: {
            screens: {
                'sm': '640px',
                'md': '768px',
                'lg': '1024px',
                'xl': '1280px',
            },
            fontSize: {
                '5xs':'.35rem',
                '4xs':'.45rem',
                '3xs':'.55rem',
                '2xs':'.65rem',
                'xs': '.75rem',
                'sm': '.875rem',
                'tiny': '.875rem',
                'base': '1rem',
                'lg': '1.125rem',
                'xl': '1.25rem',
                '2xl': '1.5rem',
                '3xl': '1.875rem',
                '4xl': '2.25rem',
                '5xl': '3rem',
                '6xl': '4rem',
                '7xl': '5rem',
            },
            colors: {
                gray: {
                    '400-opc': '#ecececd9',
                    '100': '#f5f5f5',
                    '200': '#eeeeee',
                    '300': '#e0e0e0',
                    '400': '#bdbdbd',
                    '500': '#9e9e9e',
                    '600': '#757575',
                    '700': '#616161',
                    '800': '#424242',
                    '900': '#212121',
                },
                red: {
                    '700': '#3b0d0c',
                    '600': '#621b18',
                    '500': '#cc1f1a',
                    '400': '#e3342f',
                    '300': '#ef5753',
                    '200': '#f9acaa',
                    '100': '#fcebea',
                },

                orange: {
                    '700': '#462a16',
                    '600': '#613b1f',
                    '500': '#de751f',
                    '400': '#f6993f',
                    '300': '#faad63',
                    '200': '#fcd9b6',
                    '100': '#fff5eb',
                },

                yellow: {
                    'yellow-darkest': '#453411',
                    'yellow-darker': '#684f1d',
                    'yellow-dark': '#f2d024',
                    'yellow': '#ffed4a',
                    'yellow-light': '#fff382',
                    'yellow-lighter': '#fff9c2',
                    'yellow-lightest': '#fcfbeb',
                },
                green: {
                    '700': '#0f2f21',
                    '600': '#1a4731',
                    '500': '#1f9d55',
                    '400': '#38c172',
                    '300': '#51d88a',
                    '200': '#a2f5bf',
                    '100': '#e3fcec',
                },

                teal: {
                    '700': '#0d3331',
                    '600': '#20504f',
                    '500': '#38a89d',
                    '400': '#4dc0b5',
                    '300': '#64d5ca',
                    '200': '#a0f0ed',
                    '100': '#e8fffe',
                },

                blue: {
                    '700': '#12283a',
                    '600': '#1c3d5a',
                    '500': '#2779bd',
                    '400': '#3490dc',
                    '300': '#6cb2eb',
                    '200': '#bcdefa',
                    '100': '#eff8ff',
                },

                indigo: {
                    '700': '#191e38',
                    '600': '#2f365f',
                    '500': '#5661b3',
                    '400': '#6574cd',
                    '300': '#7886d7',
                    '200': '#b2b7ff',
                    '100': '#e6e8ff',
                },

                purple: {
                    '700': '#21183c',
                    '600': '#382b5f',
                    '500': '#794acf',
                    '400': '#9561e2',
                    '300': '#a779e9',
                    '200': '#d6bbfc',
                    '100': '#f3ebff',
                },

                pink: {
                    'darkest': '#451225',
                    'darker': '#6f213f',
                    'dark': '#eb5286',
                    'normal': '#f66d9b',
                    'light': '#fa7ea8',
                    'lighter': '#ffbbca',
                }
            }
        }
    },
    variants: {
        appearance: ['responsive'],
        backgroundAttachment: ['responsive'],
        backgroundColors: ['responsive', 'hover', 'focus'],
        zIndex: ['responsive'],
        listStylePosition: ['responsive'],
        listStyleType: ['responsive']
    },
    container: {
        padding: {
            default: '1rem',
            sm: '2rem',
            lg: '4rem',
            xl: '5rem',
        },
    },
    plugins: [],
    prefix: 'tw-'
}