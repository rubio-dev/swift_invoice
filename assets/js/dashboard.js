document.addEventListener("DOMContentLoaded", function() {
    const track = document.querySelector('.carousel-track');
    const cards = Array.from(track.children);
    const prevBtn = document.querySelector('.carousel-btn.prev');
    const nextBtn = document.querySelector('.carousel-btn.next');
    let index = 1;

    function updateCarousel() {
        // Elimina todas las clases primero
        cards.forEach(card => {
            card.classList.remove('active', 'prev', 'next');
        });

        // Calcula los índices de las tarjetas visibles
        let prev = (index - 1 + cards.length) % cards.length;
        let next = (index + 1) % cards.length;

        // Agrega clases
        cards[prev].classList.add('prev');
        cards[index].classList.add('active');
        cards[next].classList.add('next');
    }

    nextBtn.addEventListener('click', () => {
        index = (index + 1) % cards.length;
        updateCarousel();
    });

    prevBtn.addEventListener('click', () => {
        index = (index - 1 + cards.length) % cards.length;
        updateCarousel();
    });

    updateCarousel();
});
