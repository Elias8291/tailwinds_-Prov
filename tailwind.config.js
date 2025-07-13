/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      // Breakpoints personalizados para móvil
      screens: {
        'xs': '475px',
        // Los breakpoints por defecto se mantienen: sm: 640px, md: 768px, lg: 1024px, xl: 1280px, 2xl: 1536px
      },
      colors: {
        primary: '#9d2449',
        'primary-dark': '#7a1d37',
        'primary-light': '#b83055',
        'primary-50': '#fdf2f5',
        'primary-100': '#fce7ec',
        'primary-200': '#f9d0db'
      },
      boxShadow: {
        'button': '0 8px 25px rgba(157, 36, 73, 0.25)',
        'button-hover': '0 12px 35px rgba(157, 36, 73, 0.35)'
      },
      // Espaciado adicional para móvil
      spacing: {
        'safe-top': 'env(safe-area-inset-top)',
        'safe-bottom': 'env(safe-area-inset-bottom)',
        'safe-left': 'env(safe-area-inset-left)',
        'safe-right': 'env(safe-area-inset-right)',
      },
      // Alturas mínimas para móvil
      minHeight: {
        'screen-safe': 'calc(100vh - env(safe-area-inset-top) - env(safe-area-inset-bottom))',
        'mobile-viewer': '400px',
        'mobile-map': '300px',
        'touch-target': '44px',
      },
      // Anchos mínimos para móvil
      minWidth: {
        'touch-target': '44px',
      },
      // Tipografía mejorada para móvil
      fontSize: {
        'xs-mobile': ['0.75rem', { lineHeight: '1.25rem' }],
        'sm-mobile': ['0.875rem', { lineHeight: '1.375rem' }],
        'base-mobile': ['1rem', { lineHeight: '1.5rem' }],
      },
      // Animaciones personalizadas
      animation: {
        'slide-in-right': 'slideInRight 0.3s ease-out',
        'slide-out-left': 'slideOutLeft 0.3s ease-in',
        'fade-in': 'fadeIn 0.2s ease-out',
        'scale-in': 'scaleIn 0.2s ease-out',
      },
      keyframes: {
        slideInRight: {
          '0%': { transform: 'translateX(100%)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },
        slideOutLeft: {
          '0%': { transform: 'translateX(0)', opacity: '1' },
          '100%': { transform: 'translateX(-100%)', opacity: '0' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        scaleIn: {
          '0%': { transform: 'scale(0.95)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
      },
      // Transiciones personalizadas
      transitionDuration: {
        '250': '250ms',
        '350': '350ms',
      },
      transitionTimingFunction: {
        'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
        'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
    },
  },
  plugins: [
    // Plugin para utilidades de móvil personalizadas
    function({ addUtilities, theme }) {
      const newUtilities = {
        '.touch-manipulation': {
          'touch-action': 'manipulation',
        },
        '.scroll-touch': {
          '-webkit-overflow-scrolling': 'touch',
        },
        '.tap-highlight-transparent': {
          '-webkit-tap-highlight-color': 'transparent',
        },
        '.select-none-mobile': {
          '-webkit-user-select': 'none',
          '-moz-user-select': 'none',
          '-ms-user-select': 'none',
          'user-select': 'none',
        },
        '.overscroll-none': {
          'overscroll-behavior': 'none',
        },
        '.overscroll-contain': {
          'overscroll-behavior': 'contain',
        },
      }
      addUtilities(newUtilities)
    },
  ],
} 