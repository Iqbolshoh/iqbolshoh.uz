/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
      },
      colors: {
        // Primary colors based on logo red
        primary: {
          50: '#fef2f2',
          100: '#fee2e2',
          200: '#fecaca',
          300: '#fca5a5',
          400: '#f87171',
          500: '#ef4444',
          600: '#dc2626',
          700: '#b91c1c',
          800: '#991b1b',
          900: '#7f1d1d',
          950: '#450a0a',
        },  
        // Accent gold colors
        accent: {
          50: '#fffbeb',
          100: '#fef3c7',
          200: '#fde68a',
          300: '#fcd34d',
          400: '#fbbf24',
          500: '#f59e0b',
          600: '#d97706',
          700: '#b45309',
          800: '#92400e',
          900: '#78350f',
          950: '#451a03',
        },
        // Logo specific colors
        logo: {
          red: '#FF0000',
          'red-light': '#FF3333',
          black: '#000000',
        },
      },
      animation: {
        'fade-in': 'fade-in 0.5s ease-out',
        'slide-up': 'slide-up 0.6s ease-out',
        'float': 'float 3s ease-in-out infinite',
        'float-icon': 'floatIcon 15s infinite linear',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'bounce-slow': 'bounce 2s infinite',
        'glow': 'glow 2s ease-in-out infinite alternate',
        'slide-in-right': 'slideInRight 0.3s ease-out',
        'slide-out-right': 'slideOutRight 0.3s ease-in',
      },
      keyframes: {
        'fade-in': {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        'slide-up': {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'float': {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        'floatIcon': {
          '0%': { transform: 'translateY(0) rotate(0deg)', opacity: '0.3' },
          '25%': { transform: 'translateY(-20px) rotate(90deg)', opacity: '0.6' },
          '50%': { transform: 'translateY(-10px) rotate(180deg)', opacity: '0.4' },
          '75%': { transform: 'translateY(-30px) rotate(270deg)', opacity: '0.7' },
          '100%': { transform: 'translateY(0) rotate(360deg)', opacity: '0.3' },
        },
        'glow': {
          '0%': { boxShadow: '0 0 5px #FF0000, 0 0 10px #FF0000, 0 0 15px #FF0000' },
          '100%': { boxShadow: '0 0 10px #FF0000, 0 0 20px #FF0000, 0 0 30px #FF0000' },
        },
        'slideInRight': {
          '0%': { transform: 'translateX(100%)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },
        'slideOutRight': {
          '0%': { transform: 'translateX(0)', opacity: '1' },
          '100%': { transform: 'translateX(100%)', opacity: '0' },
        },
      },
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
        'light-pattern': "url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23FF0000\" fill-opacity=\"0.03\"%3E%3Cpath d=\"M20 20c0-11.046 8.954-20 20-20v40c-11.046 0-20-8.954-20-20z\"/%3E%3C/g%3E%3C/svg%3E')",
      },
      boxShadow: {
        'glow-red': '0 0 20px rgba(255, 0, 0, 0.3)',
        'glow-red-lg': '0 0 40px rgba(255, 0, 0, 0.4)',
      },
      backdropBlur: {
        xs: '2px',
      },
    },
  },
  plugins: [],
};