document.addEventListener('DOMContentLoaded', function () {
    const configEl = document.getElementById('track-config');
    if (!configEl) return;

    const searchBase = configEl.dataset.searchBase;
    const initialFolio = configEl.dataset.initialFolio;

    const searchForm = document.getElementById('folio-search-form');
    const folioInput = document.getElementById('folio-input');
    const trackError = document.getElementById('track-error');
    const resultCard = document.getElementById('track-result-card');

    const resFolio = document.getElementById('res-folio');
    const resDate = document.getElementById('res-date');
    const resTitle = document.getElementById('res-title');
    const resDept = document.getElementById('res-department');
    const resNotes = document.getElementById('res-notes');
    const resImgBefore = document.getElementById('res-img-before');
    const resImgAfter = document.getElementById('res-img-after');
    const resEmptyAfter = document.getElementById('res-empty-after');

    const stepRecibido = document.getElementById('step-recibido');
    const stepProceso = document.getElementById('step-proceso');
    const stepResuelto = document.getElementById('step-resuelto');

    async function lookupFolio(folioCode) {
        if (!folioCode) return;
        trackError.classList.add('is-hidden');

        try {
            const response = await fetch(searchBase + '/' + encodeURIComponent(folioCode));
            if (!response.ok) {
                resultCard.classList.add('is-hidden');
                trackError.classList.remove('is-hidden');
                return;
            }

            const data = await response.json();
            const r = data.report;

            resFolio.textContent = r.folio;
            resDate.textContent = r.created_date;
            resTitle.textContent = r.title;
            resDept.textContent = r.department + ' • ' + r.colonia;
            resNotes.textContent = r.resolution_notes;
            resImgBefore.onerror = function() {
                this.onerror = null;
                this.src = '/images/placeholder.svg';
            };
            resImgBefore.src = r.evidence_initial || '/images/placeholder.svg';

            // Actualizar barra de estado
            stepRecibido.classList.add('is-completed');
            stepProceso.classList.toggle('is-completed', r.status === 'en_proceso' || r.status === 'resuelto');
            stepResuelto.classList.toggle('is-completed', r.status === 'resuelto');

            // Mostrar evidencia del "Después" si ya fue cargada desde el Panel Municipal
            if (r.evidence_resolution) {
                resImgAfter.src = r.evidence_resolution;
                resImgAfter.classList.remove('is-hidden');
                resEmptyAfter.classList.add('is-hidden');
            } else {
                resImgAfter.classList.add('is-hidden');
                resEmptyAfter.classList.remove('is-hidden');
            }

            resultCard.classList.remove('is-hidden');
        } catch (err) {
            resultCard.classList.add('is-hidden');
            trackError.classList.remove('is-hidden');
        }
    }

    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        lookupFolio(folioInput.value);
    });

    const quickChips = document.querySelectorAll('.folio-chip');
    quickChips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            folioInput.value = chip.dataset.folio;
            lookupFolio(chip.dataset.folio);
        });
    });

    if (initialFolio) {
        lookupFolio(initialFolio);
    }
});