@extends('citizen.layouts.app')

@section('title', 'Directorio Municipal | Álamo Reporta')

@section('content')
    <!-- BANNER HERO DEL DIRECTORIO MUNICIPAL -->
    <section class="hero-card" style="padding-bottom: 26px;">
        <svg class="hero-watermark-church" viewBox="0 0 160 100" fill="currentColor" aria-hidden="true">
            <path d="M110,95 L110,40 L105,40 L105,30 L115,30 L115,20 L118,20 L118,12 L122,12 L122,20 L125,20 L125,30 L135,30 L135,40 L130,40 L130,95 Z" opacity="0.8"/>
            <rect x="116" y="38" width="8" height="15" rx="4" fill="none" stroke="currentColor" stroke-width="2"/>
            <path d="M70,95 L70,55 Q90,45 110,55 L110,95 Z" opacity="0.6"/>
            <path d="M82,75 Q90,65 98,75 L98,95 L82,95 Z" opacity="0.4"/>
            <path d="M45,95 Q50,60 60,40 Q45,35 30,42 Q48,45 52,50 Q30,55 25,65 Q45,62 55,60 Q40,75 42,95 Z" opacity="0.7"/>
        </svg>

        <div class="hero-text">
            <span class="hero-badge">Directorio Oficial 2026-2029</span>
            <h2 class="hero-title">Directorio Municipal</h2>
            <p class="hero-subtitle">Comunícate directamente por teléfono o WhatsApp con Regidores, Directores y DIF Municipal de Álamo Temapache.</p>
        </div>

        <svg class="hero-gold-waves" viewBox="0 0 500 50" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,50 Q120,45 220,38 T420,15 Q465,10 500,2 L500,50 L0,50 Z" fill="#D4AF37" opacity="0.85"/>
            <path d="M0,50 Q140,48 250,40 T440,24 Q475,20 500,12 L500,50 L0,50 Z" fill="#F5D7A3" opacity="0.95"/>
        </svg>
    </section>

    <!-- BUSCADOR INTERACTIVO EN TIEMPO REAL -->
    <div style="margin-bottom: 16px;">
        <div style="position: relative; display: flex; align-items: center;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 18px; color: var(--accent-gold); font-size: 1rem; pointer-events: none;"></i>
            <input type="text"
                   id="directory-search-input"
                   placeholder="Buscar regidor, director, DIF u obras..."
                   style="width: 100%; height: 50px; padding: 0 46px; border-radius: 9999px; border: 1.5px solid rgba(140, 3, 38, 0.45); background: var(--bg-surface); color: #FFFFFF; font-size: 14px; font-weight: 600; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                   onfocus="this.style.borderColor='var(--accent-gold)'; this.style.boxShadow='0 0 0 4px rgba(245, 215, 163, 0.18)';"
                   onblur="this.style.borderColor='rgba(140, 3, 38, 0.45)'; this.style.boxShadow='none';">
            <button type="button" id="btn-clear-directory-search" style="display: none; position: absolute; right: 14px; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 14px; padding: 6px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- PESTAÑAS DE FILTRO POR ÁREA (REGIDORES, DIRECTORES, DIF MUNICIPAL) -->
    <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 12px; margin-bottom: 18px; scrollbar-width: none;">
        <button type="button" class="dir-filter-pill is-active" data-category="all">
            <i class="fa-solid fa-layer-group"></i> Todos ({{ count($contacts) }})
        </button>
        <button type="button" class="dir-filter-pill" data-category="regidores">
            <i class="fa-solid fa-landmark"></i> Regidores
        </button>
        <button type="button" class="dir-filter-pill" data-category="directores">
            <i class="fa-solid fa-user-tie"></i> Directores
        </button>
        <button type="button" class="dir-filter-pill" data-category="dif">
            <i class="fa-solid fa-hand-holding-heart"></i> DIF Municipal
        </button>
    </div>

    <!-- LISTADO DE CONTACTOS MUNICIPALES -->
    <div id="directory-list" style="display: flex; flex-direction: column; gap: 14px;">
        @foreach($contacts as $contact)
            <div class="dir-card" data-category="{{ $contact['category'] }}" data-search="{{ strtolower($contact['title'] . ' ' . $contact['responsible'] . ' ' . $contact['location']) }}">
                <div style="display: flex; align-items: flex-start; gap: 14px; margin-bottom: 12px;">
                    <div class="dir-icon-box">
                        <i class="{{ $contact['icon'] }}"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <span class="dir-category-badge">
                            @if($contact['category'] === 'regidores')
                                <i class="fa-solid fa-landmark"></i> Cabildo / Regiduría
                            @elseif($contact['category'] === 'directores')
                                <i class="fa-solid fa-user-tie"></i> Dirección Municipal
                            @else
                                <i class="fa-solid fa-hand-holding-heart"></i> Sistema DIF
                            @endif
                        </span>
                        <h3 class="dir-title">{{ $contact['title'] }}</h3>
                        <p class="dir-sub">{{ $contact['responsible'] }}</p>
                    </div>
                </div>

                <div class="dir-meta-wrap">
                    <div class="dir-meta-item">
                        <i class="fa-solid fa-location-dot" style="color: var(--accent-gold);"></i>
                        <span>{{ $contact['location'] }}</span>
                    </div>
                    <div class="dir-meta-item">
                        <i class="fa-solid fa-clock" style="color: var(--accent-gold);"></i>
                        <span>{{ $contact['hours'] }}</span>
                    </div>
                </div>

                <!-- ACCIONES RÁPIDAS: LLAMAR Y WHATSAPP -->
                <div class="dir-actions">
                    <a href="tel:{{ $contact['phone'] }}" class="btn-dir-call">
                        <i class="fa-solid fa-phone"></i>
                        <span>Llamar</span>
                    </a>
                    @if(!empty($contact['whatsapp']))
                        <a href="https://wa.me/{{ $contact['whatsapp'] }}?text=Hola,%20buenas%20tardes.%20Me%20comunico%20desde%20la%20App%20Ciudadana%20de%20%C3%81lamo%20Temapache."
                           target="_blank"
                           rel="noopener"
                           class="btn-dir-wa">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- MENSAJE SI NO HAY RESULTADOS DE BÚSQUEDA -->
    <div id="dir-empty-state" style="display: none; text-align: center; padding: 40px 20px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 28px; margin-top: 10px;">
        <i class="fa-solid fa-magnifying-glass" style="font-size: 2.2rem; color: var(--accent-gold); opacity: 0.6; margin-bottom: 12px; display: inline-block;"></i>
        <h4 style="font-size: 16px; font-weight: 800; color: #FFFFFF; margin-bottom: 6px;">No se encontraron contactos</h4>
        <p style="font-size: 12.5px; color: var(--text-secondary);">Intenta buscando con otra palabra o selecciona "Todos".</p>
    </div>

    <style>
        .dir-filter-pill {
            background-color: var(--bg-surface);
            border: 1.5px solid rgba(140, 3, 38, 0.45);
            color: #FFFFFF;
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .dir-filter-pill:hover {
            border-color: var(--accent-gold);
            color: var(--accent-gold);
        }

        .dir-filter-pill.is-active {
            background: var(--accent-gold);
            color: var(--accent-wine-deep);
            border-color: var(--accent-gold);
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(245, 215, 163, 0.25);
        }

        .dir-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 18px 18px;
            box-shadow: var(--shadow-card);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .dir-card:hover {
            border-color: rgba(245, 215, 163, 0.4);
            transform: translateY(-2px);
        }

        .dir-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #7A0A22;
            color: var(--accent-gold);
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .dir-category-badge {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--accent-gold);
            display: inline-block;
            margin-bottom: 2px;
        }

        .dir-title {
            font-size: 15px;
            font-weight: 800;
            color: #FFFFFF;
            line-height: 1.25;
            margin-bottom: 2px;
        }

        .dir-sub {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.35;
        }

        .dir-meta-wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: var(--bg-elevated);
            padding: 10px 14px;
            border-radius: 16px;
            margin-bottom: 12px;
            font-size: 11.5px;
            color: var(--text-secondary);
        }

        .dir-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dir-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .btn-dir-call {
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-dark) 100%);
            color: var(--accent-wine-deep);
            text-decoration: none;
            border-radius: 9999px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
            transition: transform 0.15s ease;
        }

        .btn-dir-call:hover {
            transform: translateY(-1px);
        }

        .btn-dir-wa {
            background: #25D366;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 9999px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.25);
            transition: transform 0.15s ease;
        }

        .btn-dir-wa:hover {
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('page_js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('directory-search-input');
            const btnClear = document.getElementById('btn-clear-directory-search');
            const filterPills = document.querySelectorAll('.dir-filter-pill');
            const cards = document.querySelectorAll('.dir-card');
            const emptyState = document.getElementById('dir-empty-state');

            let currentFilter = 'all';

            function filterDirectory() {
                const term = (searchInput ? searchInput.value : '').toLowerCase().trim();
                let visibleCount = 0;

                cards.forEach(function (card) {
                    const cardCategory = card.getAttribute('data-category');
                    const cardSearchText = card.getAttribute('data-search') || '';

                    const matchesCategory = (currentFilter === 'all') || (cardCategory === currentFilter);
                    const matchesSearch = !term || cardSearchText.includes(term);

                    if (matchesCategory && matchesSearch) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }

                if (btnClear) {
                    btnClear.style.display = term ? 'block' : 'none';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterDirectory);
            }

            if (btnClear) {
                btnClear.addEventListener('click', function () {
                    searchInput.value = '';
                    filterDirectory();
                    searchInput.focus();
                });
            }

            filterPills.forEach(function (pill) {
                pill.addEventListener('click', function () {
                    filterPills.forEach(p => p.classList.remove('is-active'));
                    pill.classList.add('is-active');
                    currentFilter = pill.getAttribute('data-category');
                    filterDirectory();
                });
            });
        });
    </script>
@endsection

