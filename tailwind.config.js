const plugin = require('tailwindcss/plugin')

module.exports = {
  purge: [
    './resources/views/**/*.php',
    './resources/assets/js/**/*.js',
    './resources/assets/js/**/*.vue',
    './resources/assets/sass/**/*.scss',
    './node_modules/\\@bytefury/spacewind/src/**/*.js',
    './node_modules/\\@bytefury/spacewind/src/**/*.vue',
    './node_modules/\\@bytefury/spacewind/plugin/**/*.js',
    'flatpickr/**/*.js',
    'toastr/**/*.js',
    './public/js/pace/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        base: ['Poppins', 'sans-serif'],
      },

      colors: {
        primary: {
          50: 'var(--primary-color-50)',
          100: 'var(--primary-color-100)',
          200: 'var(--primary-color-200)',
          300: 'var(--primary-color-300)',
          400: 'var(--primary-color-400)',
          500: 'var(--primary-color-500)',
          600: 'var(--primary-color-600)',
          700: 'var(--primary-color-700)',
          800: 'var(--primary-color-800)',
          900: 'var(--primary-color-900)',
        },
        black: '#040405',
      },
      spacing: {
        7: '1.75rem',
        9: '2.25rem',
        72: '18rem',
        80: '20rem',
        88: '22rem',
        96: '24rem',
      },
        screens: {
          sm: '640px',
          md: '768px',
          lg: '1024px', // Verifica que el valor sea el esperado
          xl: '1280px',
          xxl: '1440px',
      },
    },
  },
  variants: {
    textColor: ['responsive', 'hover', 'focus', 'active', 'visited'],
    borderColor: ['responsive', 'hover', 'focus', 'active', 'focus-within'],
    borderRadius: ['responsive', 'hover', 'first', 'last'],
    boxShadow: ['responsive', 'hover', 'focus', 'active', 'group-hover'],
    borderStyle: ['responsive', 'hover', 'first', 'last'],
    borderWidth: ['responsive', 'last', 'hover', 'focus'],
  },
  plugins: [
    require('@bytefury/spacewind/plugin'),

    plugin(({ config, addBase }) => {
      let craterDefaultTypography = {
        fontFamily: config('theme.fontFamily.base'),
      }
      addBase({
        '.h1': {
          ...craterDefaultTypography,
        },
        '.h2': {
          ...craterDefaultTypography,
        },
        '.h3': {
          ...craterDefaultTypography,
        },
        '.h4': {
          ...craterDefaultTypography,
        },
        '.h5': {
          ...craterDefaultTypography,
        },
        '.h6': {
          ...craterDefaultTypography,
        },
        '.page-title': {
          ...craterDefaultTypography,
        },
        '.section-title': {
          ...craterDefaultTypography,
        },
      })
    }),
  ],
}
