import confetti from 'canvas-confetti';
import Chart from 'chart.js/auto';

// Expostos em window pra poder chamar direto de scripts inline nas views
// Blade, sem precisar de um bundler por página.
window.confetti = confetti;
window.Chart = Chart;

// Dispara uma explosão de confete comemorando o vencedor do sorteio.
// Usado em resources/views/giveaways/show.blade.php quando o sorteio
// acabou de ser realizado (session 'just_drawn').
window.comemorarSorteio = function () {
    const duracao = 2200;
    const fim = Date.now() + duracao;

    const cores = ['#1F6F5C', '#E8B54B', '#E1553F', '#101816'];

    (function disparo() {
        confetti({
            particleCount: 4,
            angle: 60,
            spread: 65,
            origin: { x: 0, y: 0.6 },
            colors: cores,
        });
        confetti({
            particleCount: 4,
            angle: 120,
            spread: 65,
            origin: { x: 1, y: 0.6 },
            colors: cores,
        });

        if (Date.now() < fim) {
            requestAnimationFrame(disparo);
        }
    })();

    // Estouro central único, mais denso, no instante da revelação
    confetti({
        particleCount: 120,
        spread: 100,
        origin: { y: 0.5 },
        colors: cores,
        startVelocity: 45,
    });
};
