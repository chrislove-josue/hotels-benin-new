/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                // Charte graphique Hôtels Bénin
                'hb-blue': {
                    DEFAULT: '#1A3A6B',
                    dark: '#122A4E',
                    light: '#2C5090',
                },
                'hb-gold': {
                    DEFAULT: '#D4AF37',
                    light: '#E6CB6B',
                },
                'hb-orange': {
                    DEFAULT: '#F59A23',
                    dark: '#D9820F',
                },
                'hb-cream': '#FBF8F1',
            },
            fontFamily: {
                sans: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            boxShadow: {
                card: '0 10px 30px -12px rgba(26, 58, 107, 0.25)',
            },
        },
    },
    plugins: [],
};
