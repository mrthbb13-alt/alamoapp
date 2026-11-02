<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de Mando Municipal | Álamo Temapache</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg-main: #0F172A;
            --bg-card: #1E293B;
            --bg-elevated: #334155;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --accent-orange: #EA580C;
            --accent-yellow: #FACC15;
            --accent-green: #10B981;
            --border: #334155;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background: var(--bg-main); color: var(--text-main); min-height: 100vh; padding: 20px; }
        .container { max-width: 1300px; margin: 0 auto; }
        header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid var(--border); margin-bottom: 25px; }
        .brand h1 { font-size: 1.5rem; font-weight: 800; color: #FFF; display: flex; align-items: center; gap: 10px; }
        .brand span { font-size: 0.85rem; color: var(--accent-yellow); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .header-links a { color: #FFF; text-decoration: none; background: var(--accent-orange); padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; }
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .metric-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; }
        .metric-card .num { font-size: 2rem; font-weight: 800; color: #FFF; }
        .metric-card .lbl { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-top: 5px; }
        .card-pendientes .num { color: #F87171; }
        .card-proceso .num { color: var(--accent-yellow); }
        .card-resueltos .num { color: var(--accent-green); }
        .filter-nav { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-btn { background: var(--bg-card); color: var(--text-muted); border: 1px solid var(--border); padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .filter-btn.active { background: var(--accent-orange); color: #FFF; border-color: var(--accent-orange); }
        .table-wrap { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 18px; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        th { background: #162032; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; font-weight: 700; }
        tr:hover { background: rgba(255,255,255,0.02); }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-block; }
        .badge-pendiente { background: rgba(239, 68, 68, 0.2); color: #F87171; }
        .badge-en_proceso { background: rgba(250, 204, 21, 0.2); color: #FACC15; }
        .badge-resuelto { background: rgba(16, 185, 129, 0.2); color: #34D399; }
        .badge-rechazado { background: rgba(148, 163, 184, 0.2); color: #94A3B8; }
        .btn-action { background: var(--bg-elevated); color: #FFF; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; }
        .btn-action:hover { background: var(--accent-orange); }
        /* Modal */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; padding: 20px; z-index: 999; }
        .modal.open { display: flex; }
        .modal-content { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; width: 100%; max-width: 500px; padding: 25px; }
        .modal h3 { margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; font-weight: 600; }
        .form-control { width: 100%; padding: 10px 14px; background: var(--bg-main); border: 1px solid var(--border); border-radius: 8px; color: #FFF; font-size: 0.9rem; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-cancel { background: transparent; border: 1px solid var(--border); color: var(--text-muted); padding: 8px 16px; border-radius: 8px; cursor: pointer; }
        .btn-save { background: var(--accent-green); border: none; color: #FFF; padding: 8px 18px; border-radius: 8px; cursor: pointer; font-weight: 700; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="brand">
                <span>Gobierno Municipal</span>
                <h1><i class="fa-solid fa-tower-broadcast"></i> Centro de Mando Álamo Temapache</h1>
            </div>
            <div class="header-links">
                <a href="{{ route('citizen.home') }}" target="_blank">
                    <i class="fa-solid fa-mobile-screen"></i> Abrir App Ciudadana
                </a>
            </div>
        </header>

        <section class="metrics-grid">
            <div class="metric-card">
                <span class="num">{{ $metrics['total'] }}</span>
                <span class="lbl">Reportes Totales</span>
            </div>
            <div class="metric-card card-pendientes">
                <span class="num">{{ $metrics['pendientes'] }}</span>
                <span class="lbl">Pendientes</span>
            </div>
            <div class="metric-card card-proceso">
                <span class="num">{{ $metrics['en_proceso'] }}</span>
                <span class="lbl">En Proceso</span>
            </div>
            <div class="metric-card card-resueltos">
                <span class="num">{{ $metrics['resueltos'] }}</span>
                <span class="lbl">Resueltos</span>
            </div>
        </section>

        <div class="filter-nav">
            <a href="{{ route('municipal.dashboard') }}" class="filter-btn {{ empty($status) ? 'active' : '' }}">Todos</a>
            <a href="{{ route('municipal.dashboard', ['status' => 'pendiente']) }}" class="filter-btn {{ $status === 'pendiente' ? 'active' : '' }}">Pendientes</a>
            <a href="{{ route('municipal.dashboard', ['status' => 'en_proceso']) }}" class="filter-btn {{ $status === 'en_proceso' ? 'active' : '' }}">En Proceso</a>
            <a href="{{ route('municipal.dashboard', ['status' => 'resuelto']) }}" class="filter-btn {{ $status === 'resuelto' ? 'active' : '' }}">Resueltos</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Incidente</th>
                        <th>Departamento</th>
                        <th>Colonia / Ref.</th>
                        <th>Apoyos</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="reports-table-body">
                    @forelse($reports as $r)
                        <tr id="row-{{ $r->id }}">
                            <td><strong>{{ $r->folio }}</strong></td>
                            <td>{{ $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $r->title }}</td>
                            <td>{{ $r->department }}</td>
                            <td>{{ $r->colonia }}</td>
                            <td>+{{ $r->upvotes }}</td>
                            <td><span class="badge badge-{{ $r->status }}">{{ strtoupper(str_replace('_', ' ', $r->status)) }}</span></td>
                            <td>
                                <button type="button" class="btn-action" onclick="openUpdateModal({{ $r->id }}, '{{ $r->folio }}', '{{ $r->status }}', '{{ addslashes($r->resolution_notes ?? '') }}')">
                                    <i class="fa-solid fa-pen-to-square"></i> Gestionar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                No se encontraron reportes en esta categoría.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Actualizar Estatus -->
    <div id="update-modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-title">Gestionar Reporte</h3>
            <form id="update-status-form" enctype="multipart/form-data">
                <input type="hidden" id="modal-report-id">
                <div class="form-group">
                    <label>Nuevo Estado</label>
                    <select id="modal-status" name="status" class="form-control" required>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="resuelto">Resuelto</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notas de Dictamen / Cuadrilla</label>
                    <textarea id="modal-notes" name="resolution_notes" class="form-control" rows="3" placeholder="Detalles de la reparación o motivo de rechazo..."></textarea>
                </div>
                <div class="form-group">
                    <label>Evidencia de Reparación (Foto "Después")</label>
                    <input type="file" id="modal-evidence" name="evidence_file" class="form-control" accept="image/*">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeUpdateModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const modal = document.getElementById('update-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalReportId = document.getElementById('modal-report-id');
        const modalStatus = document.getElementById('modal-status');
        const modalNotes = document.getElementById('modal-notes');
        const updateForm = document.getElementById('update-status-form');

        function openUpdateModal(id, folio, status, notes) {
            modalReportId.value = id;
            modalTitle.textContent = 'Gestionar Folio: ' + folio;
            modalStatus.value = status;
            modalNotes.value = notes || '';
            modal.classList.add('open');
        }

        function closeUpdateModal() {
            modal.classList.remove('open');
            updateForm.reset();
        }

        updateForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = modalReportId.value;
            const formData = new FormData(updateForm);

            try {
                const res = await fetch('/panel-municipal/reportes/' + id + '/actualizar', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error al actualizar el reporte');
                }
            } catch (err) {
                alert('Error de conexión');
            }
        });
    </script>
</body>
</html>

