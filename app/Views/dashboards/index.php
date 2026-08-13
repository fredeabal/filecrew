<!-- =====================================================================
     CABECERA Y BREADCRUMB (NAVEGACIÓN)
     ===================================================================== -->
<div class="card border shadow-none position-relative overflow-hidden mb-4">
  <div class="card-body px-4 py-3">
    <div class="row align-items-center">
      <div class="col-9">
        <h4 class="fw-semibold mb-8">Panel de Usuario</h4>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a class="text-muted text-decoration-none" href="<?= base_url() ?>">Inicio</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Dashboard</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>

<!-- =====================================================================
     TARJETAS DE ESTADÍSTICAS (MÉTRICAS DEL USUARIO)
     ===================================================================== -->
<div class="row">
    <!-- Card Mis Archivos -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card border shadow-none h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="card-subtitle text-muted mb-0">Archivos</h6>
                    <div class="bg-primary-subtle text-primary rounded p-2">
                        <i class="ti ti-files fs-6"></i>
                    </div>
                </div>
                <h3 class="card-title mb-0 fw-semibold"><?= esc($filesCount ?? 0) ?></h3>
                <p class="card-text text-muted fs-2 mt-2">Archivos subidos por tu usuario.</p>
            </div>
        </div>
    </div>

    <!-- Card Mis Contraseñas -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card border shadow-none h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="card-subtitle text-muted mb-0">Contraseñas</h6>
                    <div class="bg-secondary-subtle text-secondary rounded p-2">
                        <i class="ti ti-key fs-6"></i>
                    </div>
                </div>
                <h3 class="card-title mb-0 fw-semibold"><?= esc($passwordsCount ?? 0) ?></h3>
                <p class="card-text text-muted fs-2 mt-2">Contraseñas creadas por tu usuario.</p>
            </div>
        </div>
    </div>

    <!-- Card Espacio Utilizado -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card border shadow-none h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="card-subtitle text-muted mb-0">Espacio Utilizado</h6>
                    <div class="bg-success-subtle text-success rounded p-2">
                        <i class="ti ti-chart-pie fs-6"></i>
                    </div>
                </div>
                <h3 class="card-title mb-0 fw-semibold"><?= esc($spaceUsed ?? '0 MB') ?></h3>
                <p class="card-text text-muted fs-2 mt-2">Espacio total de tus archivos subidos.</p>
            </div>
        </div>
    </div>

    <!-- Card Descargas de mis Enlaces -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card border shadow-none h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="card-subtitle text-muted mb-0">Descargas Visualizadas</h6>
                    <div class="bg-info-subtle text-info rounded p-2">
                        <i class="ti ti-download fs-6"></i>
                    </div>
                </div>
                <h3 class="card-title mb-0 fw-semibold"><?= esc($downloadsCount ?? 0) ?></h3>
                <p class="card-text text-muted fs-2 mt-2">Descargas totales de tus archivos compartidos.</p>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     TABLA DE ÚLTIMOS ENVÍOS
     ===================================================================== -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border shadow-none">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
                    <h5 class="card-title fw-semibold mb-0">Últimos Envíos Realizados</h5>
                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto search-form-responsive">
                        <!-- Buscador (Filtro en tiempo real como en /users) -->
                        <div class="position-relative search-box-container w-100 w-md-auto">
                            <input type="text" id="search-logs" class="form-control" placeholder="Buscar...">
                            <i class="ti ti-search search-icon text-muted"></i>
                        </div>
                        <!-- Selector de Límite -->
                        <select id="limitSelector" class="form-select w-auto">
                            <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>Ver 5</option>
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>Ver 10</option>
                            <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>Ver 25</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>Ver 50</option>
                        </select>
                    </div>
                </div>
                
                <?php if (empty($recentShares)): ?>
                    <div class="text-center py-4">
                        <i class="ti ti-mail text-muted fs-7 mb-2 d-block"></i>
                        <p class="text-muted mb-0">Aún no se han realizado envíos de archivos o contraseñas.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle text-nowrap mb-0 table-hover-custom">
                            <thead class="text-dark fs-3">
                                <tr>
                                    <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0 text-muted">Fecha</h6></th>
                                    <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-muted">Remitente</h6></th>
                                    <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-muted">Destinatario</h6></th>
                                    <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-muted">Tipo</h6></th>
                                    <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-muted">Nombre</h6></th>
                                    <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-muted">Estado</h6></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentShares as $log): ?>
                                    <tr class="table-row-hover">
                                        <td class="border-bottom-0 text-center">
                                            <h6 class="fs-3 fw-semibold mb-0"><?= date('d/m/Y', strtotime($log->created_at)) ?></h6>
                                            <span class="fw-normal text-muted text-login-time"><?= date('H:i', strtotime($log->created_at)) ?></span>
                                        </td>
                                        <td class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0 fs-3"><?= esc($log->sender_username) ?></h6>
                                        </td>
                                        <td class="border-bottom-0">
                                            <span class="fs-3"><?= esc($log->recipient_email) ?></span>
                                        </td>
                                        <td class="border-bottom-0">
                                            <span class="fs-3"><?= $log->resource_type === 'file' ? 'Archivo' : 'Contraseña' ?></span>
                                        </td>
                                        <td class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0 fs-3 text-truncate text-truncate-max-200" title="<?= esc($log->resource_name) ?>">
                                                <?= esc($log->resource_name) ?>
                                            </h6>
                                        </td>
                                        <td class="border-bottom-0">
                                            <?php if ($log->status === 'success'): ?>
                                                <span class="badge bg-success badge-status">Enviado</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger badge-status">Error</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Selector de límite
document.getElementById('limitSelector')?.addEventListener('change', function() {
    const limit = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set('limit', limit);
    window.location.href = url.toString();
});

// Buscador en tiempo real (idéntico al de usuarios)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-logs');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('table.table tbody tr.table-row-hover');
            
            rows.forEach(row => {
                const senderEl = row.querySelector('td:nth-child(2) h6');
                const recipientEl = row.querySelector('td:nth-child(3) span');
                const nameEl = row.querySelector('td:nth-child(5) h6');
                
                const senderText = senderEl ? senderEl.textContent.toLowerCase() : '';
                const recipientText = recipientEl ? recipientEl.textContent.toLowerCase() : '';
                const nameText = nameEl ? nameEl.textContent.toLowerCase() : '';
                
                if (senderText.includes(filter) || recipientText.includes(filter) || nameText.includes(filter)) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                }
            });
        });
    }
});
</script>

