    // assets/js/catalogo_scripts.js
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800, // Duração da animação
        once: true,    // Animar apenas uma vez
    });

    // Inicializa os carrosseis Swiper para cada card de árvore
    const swiperContainers = document.querySelectorAll('[class*="gallery-swiper-"]');
    swiperContainers.forEach(container => {
        if (container.classList.contains('swiper-initialized')) return; // Evitar reinicialização

        new Swiper(container, {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 15, // Reduzido um pouco para telas menores
            grabCursor: true,
            pagination: {
                el: container.querySelector('.swiper-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: container.querySelector('.swiper-button-next'),
                prevEl: container.querySelector('.swiper-button-prev'),
            },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 25 } // Ajustado para melhor visualização
            },
            on: {
                init: function () {
                    container.classList.add('swiper-initialized');
                },
            },
        });
    });

    // Inicializa botões de expandir/recolher para detalhes da árvore
    const expandButtons = document.querySelectorAll('.expand-btn');
    expandButtons.forEach(button => {
        button.addEventListener('click', function() {
            const article = this.closest('article.tree-card');
            if (!article) return;

            const detailsId = this.getAttribute('aria-controls');
            const details = document.getElementById(detailsId); // Usar ID para buscar
            if (!details) return;

            const isExpanded = details.classList.contains('max-h-[9999px]');

            // Recolhe outros cards abertos antes de expandir o atual
            if (!isExpanded) {
                document.querySelectorAll('article.tree-card .tree-details.max-h-\\[9999px\\]').forEach(openDetail => {
                    if (openDetail !== details) {
                        openDetail.classList.remove('max-h-[9999px]');
                        openDetail.classList.add('max-h-0');
                        const otherButton = openDetail.closest('article.tree-card').querySelector('.expand-btn');
                        if (otherButton) {
                            otherButton.textContent = 'Expandir';
                            otherButton.setAttribute('aria-expanded', 'false');
                        }
                    }
                });
            }

            // Alterna o estado do card clicado
            if (isExpanded) {
                details.classList.remove('max-h-[9999px]');
                details.classList.add('max-h-0');
                this.textContent = 'Expandir';
                this.setAttribute('aria-expanded', 'false');
            } else {
                details.classList.remove('max-h-0');
                details.classList.add('max-h-[9999px]');
                this.textContent = 'Recolher';
                this.setAttribute('aria-expanded', 'true');

                // Atualiza o Swiper dentro do detalhe expandido, se existir e estiver inicializado
                const swiperInstanceEl = details.querySelector('[class*="gallery-swiper-"]');
                if (swiperInstanceEl && swiperInstanceEl.swiper) {
                    swiperInstanceEl.swiper.update();
                    swiperInstanceEl.swiper.lazy.load(); // Se usar lazy loading
                }
                 // Rola a página para que o cabeçalho do card fique visível (opcional)
                // article.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});