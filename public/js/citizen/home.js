document.addEventListener('DOMContentLoaded', function () {
    const configEl = document.getElementById('home-config');
    if (!configEl) return;

    const storeUrl = configEl.dataset.storeUrl;
    const nearbyUrl = configEl.dataset.nearbyUrl;
    const upvoteBase = configEl.dataset.upvoteBase;
    const trackBaseUrl = configEl.dataset.trackUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Mapeo de imágenes de referencia locales por categoría
    const categoryRefImages = {
        'bache': {
            'src': '/images/categories/ref_bache.jpg',
            'name': 'ref_bache.jpg',
            'desc': 'Imagen sugerida para bache o daño en calle'
        },
        'drenaje': {
            'src': '/images/categories/ref_drenaje.jpg',
            'name': 'ref_drenaje.jpg',
            'desc': 'Imagen sugerida para tapa o alcantarilla de drenaje'
        },
        'alumbrado': {
            'src': '/images/categories/ref_alumbrado.jpg',
            'name': 'ref_alumbrado.jpg',
            'desc': 'Imagen sugerida para luminaria pública'
        },
        'agua': {
            'src': '/images/categories/ref_agua.jpg',
            'name': 'ref_agua.jpg',
            'desc': 'Imagen sugerida para fuga de agua potable'
        },
        'basura': {
            'src': '/images/categories/ref_basura.jpg',
            'name': 'ref_basura.jpg',
            'desc': 'Imagen sugerida para acumulación de basura'
        },
        'accidente': {
            'src': '/images/categories/ref_accidente.jpg',
            'name': 'ref_accidente.jpg',
            'desc': 'Imagen sugerida para accidente vial o riesgo'
        }
    };

    let hasUserPhoto = false;

    // 1. Selección de Categoría
    const categoryTiles = document.querySelectorAll('.category-tile');
    const categoryInput = document.getElementById('selected-category');

    categoryTiles.forEach(function (tile) {
        tile.addEventListener('click', function () {
            categoryTiles.forEach(function (t) {
                t.classList.remove('is-selected');
            });
            tile.classList.add('is-selected');
            const cat = tile.dataset.category;
            categoryInput.value = cat;

            // Si el usuario no ha subido foto propia, actualizar la imagen de referencia sugerida
            if (!hasUserPhoto && categoryRefImages[cat]) {
                updateReferencePhoto(cat);
            }

            verifyNearbyDuplicate();
        });
    });

    // 2. Control de Fotografía (Referencia Automática + Opción de Foto Real)
    const evidenceInput = document.getElementById('evidence-input');
    const btnTriggerUpload = document.getElementById('btn-trigger-upload');
    const btnUploadText = document.getElementById('btn-upload-text');
    const photoPreviewThumb = document.getElementById('photo-preview-thumb');
    const photoFileName = document.getElementById('photo-file-name');
    const photoFileDesc = document.getElementById('photo-file-desc');
    const photoStatusBadge = document.getElementById('photo-status-badge');
    const photoBadgeText = document.getElementById('photo-badge-text');
    const btnClearPhoto = document.getElementById('btn-clear-photo');
    const btnViewPhotoLightbox = document.getElementById('btn-view-photo-lightbox');

    // Lightbox modal para ampliar foto con fondo transparente
    const homeLightboxModal = document.getElementById('home-lightbox-modal');
    const homeLightboxImg = document.getElementById('home-lightbox-img');
    const btnCloseHomeLightbox = document.getElementById('btn-close-home-lightbox');

    let currentPhotoUrl = '/images/categories/ref_bache.jpg';
    if (homeLightboxImg) homeLightboxImg.src = currentPhotoUrl;

    function updateReferencePhoto(cat) {
        const ref = categoryRefImages[cat] || categoryRefImages['bache'];
        currentPhotoUrl = ref.src;
        if (photoPreviewThumb) photoPreviewThumb.src = ref.src;
        if (homeLightboxImg) homeLightboxImg.src = ref.src;
        if (photoFileName) photoFileName.textContent = ref.name;
        if (photoFileDesc) photoFileDesc.textContent = ref.desc;
        if (photoBadgeText) photoBadgeText.textContent = 'Imagen de referencia';
        if (photoStatusBadge) {
            photoStatusBadge.style.color = '#EA580C';
            photoStatusBadge.style.background = 'rgba(234, 88, 12, 0.12)';
        }
        if (btnClearPhoto) btnClearPhoto.style.display = 'none';
        if (btnUploadText) btnUploadText.textContent = 'Tomar foto real o elegir de galería';
    }

    if (btnTriggerUpload && evidenceInput) {
        btnTriggerUpload.addEventListener('click', function () {
            evidenceInput.click();
        });
    }

    if (evidenceInput) {
        evidenceInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            hasUserPhoto = true;
            if (photoFileName) photoFileName.textContent = file.name;
            if (photoFileDesc) photoFileDesc.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB • Foto adjunta por ti';
            if (photoBadgeText) photoBadgeText.textContent = 'Tu foto real adjunta';
            if (photoStatusBadge) {
                photoStatusBadge.style.color = '#10B981';
                photoStatusBadge.style.background = 'rgba(16, 185, 129, 0.15)';
            }
            if (btnClearPhoto) btnClearPhoto.style.display = 'inline-flex';
            if (btnUploadText) btnUploadText.textContent = 'Cambiar foto (Cámara / Galería)';

            const reader = new FileReader();
            reader.onload = function (event) {
                currentPhotoUrl = event.target.result;
                if (photoPreviewThumb) photoPreviewThumb.src = currentPhotoUrl;
                if (homeLightboxImg) homeLightboxImg.src = currentPhotoUrl;
            };
            reader.readAsDataURL(file);
        });
    }

    function removeSelectedPhoto() {
        if (evidenceInput) evidenceInput.value = '';
        hasUserPhoto = false;
        const currentCat = categoryInput ? categoryInput.value : 'bache';
        updateReferencePhoto(currentCat);
    }

    if (btnClearPhoto) btnClearPhoto.addEventListener('click', removeSelectedPhoto);

    // Abrir visor Lightbox
    function openLightbox() {
        if (currentPhotoUrl && homeLightboxModal) {
            homeLightboxModal.style.display = 'flex';
        }
    }

    if (btnViewPhotoLightbox) btnViewPhotoLightbox.addEventListener('click', openLightbox);
    if (photoPreviewThumb) photoPreviewThumb.addEventListener('click', openLightbox);

    // Cerrar visor
    if (btnCloseHomeLightbox) {
        btnCloseHomeLightbox.addEventListener('click', function () {
            if (homeLightboxModal) homeLightboxModal.style.display = 'none';
        });
    }

    if (homeLightboxModal) {
        homeLightboxModal.addEventListener('click', function (e) {
            if (e.target === homeLightboxModal) {
                homeLightboxModal.style.display = 'none';
            }
        });
    }

    // 3. Mapa Interactivo centrado en Álamo Temapache (OpenStreetMap - Sin API Key)
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
    if (btnLocate) {
        btnLocate.addEventListener('click', function () {
            if (!navigator.geolocation) return;
            btnLocate.textContent = 'Obteniendo GPS...';
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.flyTo([lat, lng], 17);
                    pinMarker.setLatLng([lat, lng]);
                    updateCoordinates(lat, lng);
                    btnLocate.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Ubicación GPS actualizada';
                },
                function () {
                    btnLocate.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Mueve el pin manualmente';
                }
            );
        });
    }

    // 4. Detección Inteligente de Reportes Duplicados Cercanos
    const duplicateAlert = document.getElementById('duplicate-alert');
    const dupTitle = document.getElementById('dup-title');
    const dupMeta = document.getElementById('dup-meta');
    const btnUpvoteExisting = document.getElementById('btn-upvote-existing');
    let currentDuplicateId = null;

    async function verifyNearbyDuplicate() {
        if (!inputLat || !inputLng || !categoryInput) return;
        const lat = inputLat.value;
        const lng = inputLng.value;
        const cat = categoryInput.value;

        try {
            const response = await fetch(nearbyUrl + '?lat=' + lat + '&lng=' + lng + '&category=' + cat);
            const data = await response.json();

            if (data.found && data.report) {
                currentDuplicateId = data.report.id;
                dupTitle.textContent = data.report.folio + ' — ' + data.report.title;
                dupMeta.textContent = 'Colonia: ' + data.report.colonia + ' | Apoyos: +' + data.report.upvotes;
                duplicateAlert.classList.remove('is-hidden');
            } else {
                currentDuplicateId = null;
                duplicateAlert.classList.add('is-hidden');
            }
        } catch (err) {
            duplicateAlert.classList.add('is-hidden');
        }
    }

    if (btnUpvoteExisting) {
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
    }

    // 5. Envío del Formulario al Servidor
    const reportForm = document.getElementById('citizen-report-form');
    const btnSubmit = document.getElementById('btn-submit-report');
    const btnSubmitText = document.getElementById('btn-submit-text');
    const errorBanner = document.getElementById('form-error-msg');
    const successBanner = document.getElementById('success-banner');
    const createdFolioBox = document.getElementById('created-folio-box');
    const btnGoTrack = document.getElementById('btn-go-track');
    const btnNewReport = document.getElementById('btn-new-report');

    if (reportForm) {
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
    }

    if (btnNewReport) {
        btnNewReport.addEventListener('click', function () {
            reportForm.reset();
            removeSelectedPhoto();
            successBanner.classList.add('is-hidden');
            reportForm.classList.remove('is-hidden');
        });
    }

    verifyNearbyDuplicate();
});
