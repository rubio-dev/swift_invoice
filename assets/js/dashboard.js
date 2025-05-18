// Array de módulos (puedes modificar aquí los datos de cada uno)
const modules = [
    {
        href: "/swift_invoice/modules/sales/",
        icon: "💼",
        title: "Ventas",
        desc: "Crear factura de venta",
        link: "Acceder →"
    },
    {
        href: "/swift_invoice/modules/clients/",
        icon: "👥",
        title: "Clientes",
        desc: "Administrar lista de clientes",
        link: "Ver todos →"
    },
    {
        href: "/swift_invoice/modules/invoices/",
        icon: "📋",
        title: "Facturas",
        desc: "Historial de facturación",
        link: "Consultar →"
    },
    {
        href: "/swift_invoice/modules/company/",
        icon: "🏛️",
        title: "Compañías",
        desc: "Gestionar empresas",
        link: "Administrar →"
    }
];

document.addEventListener("DOMContentLoaded", function() {
    // Selecciona las tarjetas fijas
    const cards = document.querySelectorAll('.carousel-track .action-card');
    const prevBtn = document.querySelector('.carousel-btn.prev');
    const nextBtn = document.querySelector('.carousel-btn.next');
    let index = 1; // Comienza en la segunda tarjeta (centrada)

    // Devuelve el módulo correspondiente al índice (con loop infinito)
    function getModule(idx) {
        return modules[(idx + modules.length) % modules.length];
    }

    // Rellena una tarjeta con los datos del módulo
    function renderCard(card, mod) {
        card.innerHTML = `
            <a href="${mod.href}" style="text-decoration: none; color: inherit; display: block;">
                <div class="card-icon-container"><span class="card-icon">${mod.icon}</span></div>
                <div class="card-content">
                    <h3 class="card-title">${mod.title}</h3>
                    <p class="card-description">${mod.desc}</p>
                    <span class="card-link">${mod.link}</span>
                </div>
            </a>
        `;
    }

    // Actualiza las 3 tarjetas (prev, active, next)
    function updateCarousel() {
        renderCard(cards[0], getModule(index - 1));
        renderCard(cards[1], getModule(index));
        renderCard(cards[2], getModule(index + 1));
    }

    // Navegación
    nextBtn.addEventListener('click', () => {
        index = (index + 1) % modules.length;
        updateCarousel();
    });

    prevBtn.addEventListener('click', () => {
        index = (index - 1 + modules.length) % modules.length;
        updateCarousel();
    });

    // Inicializa el carrusel
    updateCarousel();
});
