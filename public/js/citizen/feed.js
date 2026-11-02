document.addEventListener('DOMContentLoaded', function () {
    const configEl = document.getElementById('feed-config');
    if (!configEl) return;

    const upvoteBase = configEl.dataset.upvoteBase;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const map = L.map('community-map').setView([20.9114, -97.6744], 14);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markersGroup = L.layerGroup().addTo(map);
    const reportCards = document.querySelectorAll('.report-card');

    const selectedPinCard = document.getElementById('map-selected-card');
    const pinCardFolio = document.getElementById('pin-card-folio');
    const pinCardTitle = document.getElementById('pin-card-title');
    const pinCardLocation = document.getElementById('pin-card-location');
    const btnClosePinCard = document.getElementById('btn-close-pin-card');

    if (btnClosePinCard) {
        btnClosePinCard.addEventListener('click', function () {
            selectedPinCard.classList.add('is-hidden');
        });
    }

    function renderMarkers(activeFilter) {
        markersGroup.clearLayers();

        reportCards.forEach(function (card) {
            const category = card.dataset.category;
            const match = activeFilter === 'all' || category === activeFilter;

            if (!match) {
                card.classList.add('is-hidden');
                return;
            }

            card.classList.remove('is-hidden');

            const lat = parseFloat(card.dataset.lat);
            const lng = parseFloat(card.dataset.lng);
            const status = card.dataset.status;

            const fillColor = status === 'resuelto' ? '#10B981' : (status === 'en_proceso' ? '#FACC15' : '#EA580C');

            const circle = L.circleMarker([lat, lng], {
                radius: 9,
                fillColor: fillColor,
                color: '#FFFFFF',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            });

            circle.on('click', function () {
                pinCardFolio.textContent = card.dataset.folio;
                pinCardTitle.textContent = card.dataset.title;
                pinCardLocation.textContent = 'Colonia: ' + card.dataset.colonia;
                selectedPinCard.classList.remove('is-hidden');
            });

            markersGroup.addLayer(circle);
        });
    }

    // Filtros por categoría
    const filterPills = document.querySelectorAll('.filter-pill');
    filterPills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            filterPills.forEach(function (p) {
                p.classList.remove('is-active');
            });
            pill.classList.add('is-active');
            renderMarkers(pill.dataset.filter);
        });
    });

    // Botón "Ver en mapa" en cada tarjeta
    const focusBtns = document.querySelectorAll('.btn-focus-map');
    focusBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const lat = parseFloat(btn.dataset.lat);
            const lng = parseFloat(btn.dataset.lng);
            map.flyTo([lat, lng], 17);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Botón "+1 Me afecta" en cada tarjeta
    const upvoteBtns = document.querySelectorAll('.btn-upvote');
    upvoteBtns.forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id = btn.dataset.id;
            const countEl = btn.querySelector('.upvote-count');

            btn.disabled = true;
            try {
                const res = await fetch(upvoteBase + '/' + id, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success && countEl) {
                    countEl.textContent = data.upvotes;
                }
            } catch (e) {
                btn.disabled = false;
            }
        });
    });

    renderMarkers('all');
});