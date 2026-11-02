document.addEventListener('DOMContentLoaded', function () {
    const configEl = document.getElementById('home-config');
    if (!configEl) return;

    const storeUrl = configEl.dataset.storeUrl;
    const nearbyUrl = configEl.dataset.nearbyUrl;
    const upvoteBase = configEl.dataset.upvoteBase;
    const trackBaseUrl = configEl.dataset.trackUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 1. Selección de Categoría
    const categoryTiles = document.querySelectorAll('.category-tile');
    const categoryInput = document.getElementById('selected-category');

    categoryTiles.forEach(function (tile) {
        tile.addEventListener('click', function () {
            categoryTiles.forEach(function (t) {
                t.classList.remove('is-selected');
            });
            tile.classList.add('is-selected');
            categoryInput.value = tile.dataset.category;
            verifyNearbyDuplicate();
        });
    });

    // 2. Previsualización de Fotografía
    const evidenceInput = document.getElementById('evidence-input');
    const photoPlaceholder = document.getElementById('photo-placeholder');
    const photoPreviewContainer = document.getElementById('photo-preview-container');
    const photoPreviewImg = document.getElementById('photo-preview-img');

    evidenceInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            photoPreviewImg.src = event.target.result;
            photoPlaceholder.classList.add('is-hidden');
            photoPreviewContainer.classList.remove('is-hidden');
        };
        reader.readAsDataURL(file);
    });

    // 3. Mapa Interactivo centrado en Álamo Temapache
    const defaultLat = 20.9114;
    const defaultLng = -97.6744;
    const map = L.map('citizen-map').setView([defaultLat, defaultLng], 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const pinMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    const inputLat = document.getElementById('input-lat');
    const inputLng = document.getElementById('input-lng');
    const coordsDisplay = document.getElementById('coords-display');

    function updateCoordinates(lat, lng) {
        const fixedLat = lat.toFixed(6);
        const fixedLng = lng.toFixed(6);
        inputLat.value = fixedLat;
        inputLng.value = fixedLng;
        coordsDisplay.textContent = fixedLat + ', ' + fixedLng;
        verifyNearbyDuplicate();
    }

    pinMarker.on('dragend', function () {
        const pos = pinMarker.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        pinMarker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    // Botón GPS actual
    const btnLocate = document.getElementById('btn-locate-me');
    btnLocate.addEventListener('click', function () {
        if (!navigator.geolocation) return;
        btnLocate.textContent = 'Obteniendo ubicación GPS...';
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.flyTo([lat, lng], 17);
                pinMarker.setLatLng([lat, lng]);
                updateCoordinates(lat, lng);
                btnLocate.textContent = 'Ubicación GPS actualizada';
            },
            function () {
                btnLocate.textContent = 'No se pudo acceder al GPS (mueve el pin manualmente)';
            }
        );
    });

    // 4. Detección Inteligente de Reportes Duplicados Cercanos
    const duplicateAlert = document.getElementById('duplicate-alert');
    const dupTitle = document.getElementById('dup-title');
    const dupMeta = document.getElementById('dup-meta');
    const btnUpvoteExisting = document.getElementById('btn-upvote-existing');
    let currentDuplicateId = null;

    async function verifyNearbyDuplicate() {
        const lat = inputLat.value;
        const lng = inputLng.value;
        const cat = categoryInput.value;

        try {
            const response = await fetch(nearbyUrl + '?lat=' + lat + '&lng=' + lng + '&category=' + cat);
            const data = await response.json();

            if (data.found && data.report) {
                currentDuplicateId = data.report.id;
                dupTitle.textContent = data.report.folio + ' — ' + data.report.title;
                dupMeta.textContent = 'Colonia: ' + data.report.colonia + ' | Apoyos actuales: +' + data.report.upvotes;
                duplicateAlert.classList.remove('is-hidden');
            } else {
                currentDuplicateId = null;
                duplicateAlert.classList.add('is-hidden');
            }
        } catch (err) {
            duplicateAlert.classList.add('is-hidden');
        }
    }

    btnUpvoteExisting.addEventListener('click', async function () {
        if (!currentDuplicateId) return;
        try {
            const res = await fetch(upvoteBase + '/' + currentDuplicateId, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await res.json();
            if (data.success) {
                btnUpvoteExisting.textContent = '¡Listo! Te sumaste al folio ' + data.folio + ' (+' + data.upvotes + ')';
                btnUpvoteExisting.disabled = true;
            }
        } catch (err) {
            console.error(err);
        }
    });

    // 5. Envío del Formulario al Servidor
    const reportForm = document.getElementById('citizen-report-form');
    const btnSubmit = document.getElementById('btn-submit-report');
    const btnSubmitText = document.getElementById('btn-submit-text');
    const errorBanner = document.getElementById('form-error-msg');
    const successBanner = document.getElementById('success-banner');
    const createdFolioBox = document.getElementById('created-folio-box');
    const btnGoTrack = document.getElementById('btn-go-track');
    const btnNewReport = document.getElementById('btn-new-report');

    reportForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorBanner.classList.add('is-hidden');
        btnSubmit.disabled = true;
        btnSubmitText.textContent = 'Enviando reporte al Centro de Mando...';

        const formData = new FormData(reportForm);

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                createdFolioBox.textContent = result.folio;
                btnGoTrack.href = trackBaseUrl + '?folio=' + result.folio;
                reportForm.classList.add('is-hidden');
                successBanner.classList.remove('is-hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                errorBanner.textContent = result.message || 'Verifica que hayas subido una fotografía válida.';
                errorBanner.classList.remove('is-hidden');
            }
        } catch (error) {
            errorBanner.textContent = 'Ocurrió un error de conexión al enviar el reporte.';
            errorBanner.classList.remove('is-hidden');
        } finally {
            btnSubmit.disabled = false;
            btnSubmitText.textContent = 'Enviar Reporte al Municipio';
        }
    });

    btnNewReport.addEventListener('click', function () {
        reportForm.reset();
        photoPlaceholder.classList.remove('is-hidden');
        photoPreviewContainer.classList.add('is-hidden');
        successBanner.classList.add('is-hidden');
        reportForm.classList.remove('is-hidden');
    });

    verifyNearbyDuplicate();
});