/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#101816',       // quase-preto esverdeado — "tinta de carimbo"
                paper: '#F1F4F2',     // fundo frio, neutro — não é o creme genérico
                card: '#FFFFFF',
                teal: {
                    DEFAULT: '#1F6F5C', // verde-azulado — dinheiro/Pix, sem copiar o teal oficial do Pix
                    dark: '#164F41',
                    light: '#E4F1EC',
                },
                gold: {
                    DEFAULT: '#E8B54B', // "bilhete premiado"
                    dark: '#C6941F',
                    light: '#FBF0D9',
                },
                coral: {
                    DEFAULT: '#E1553F', // alertas / pendências
                    light: '#FBE7E3',
                },
            },
            fontFamily: {
                display: ['"Fraunces"', 'serif'],
                sans: ['"Inter"', 'sans-serif'],
                mono: ['"IBM Plex Mono"', 'monospace'],
            },
        },
    },
    plugins: [],
}
