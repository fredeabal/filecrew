<!-- =====================================================================
     CABECERA Y BREADCRUMB (NAVEGACIÓN)
     ===================================================================== -->
<div class="card border shadow-none position-relative overflow-hidden mb-4">
  <div class="card-body px-4 py-3">
    <div class="row align-items-center">
      <div class="col-12">
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
<!-- =====================================================================
     TABLA DE ÚLTIMOS ENVÍOS REALIZADOS (MEJORADA UX/UI)
     ===================================================================== -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card dashboard-table-card shadow-none">
            <div class="card-body p-4">
                <!-- Barra superior: Título, Métricas y Filtros -->
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-light-primary text-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-send fs-6"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="card-title fw-semibold mb-0">Últimos Envíos</h5>
                                <?php if (!empty($totalRecentShares)): ?>
                                    <span class="badge bg-light-primary text-primary rounded-pill fs-2 px-2 py-1">
                                        <?= esc($totalRecentShares) ?> <?= $totalRecentShares === 1 ? 'envío' : 'envíos' ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted fs-2">Historial de transferencias y notificaciones enviadas por correo</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto search-form-responsive">
                        <!-- Buscador en tiempo real -->
                        <div class="position-relative search-box-container dashboard-search-wrap w-100 w-md-auto">
                            <input type="text" id="search-logs" class="form-control" placeholder="Buscar por recurso, correo...">
                            <i class="ti ti-search search-icon text-muted"></i>
                            <button type="button" id="clear-search-logs" class="btn btn-sm border-0 position-absolute end-0 top-50 translate-middle-y text-muted d-none me-2" title="Limpiar búsqueda">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <?php if (empty($recentShares)): ?>
                    <!-- Estado Vacío Ilustrado -->
                    <div class="text-center py-5">
                        <div class="d-inline-flex p-3 rounded-circle bg-light-primary text-primary mb-3">
                            <i class="ti ti-send-off fs-8"></i>
                        </div>
                        <h5 class="fw-semibold mb-1">Sin envíos registrados</h5>
                        <p class="text-muted mb-4 mx-auto dashboard-empty-desc">
                            Aún no has enviado notificaciones de archivos o contraseñas por correo electrónico. Cuando realices un envío, su trazabilidad aparecerá aquí.
                        </p>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="<?= base_url('files/upload') ?>" class="btn btn-primary d-inline-flex align-items-center gap-1">
                                <i class="ti ti-upload"></i> Subir Archivo
                            </a>
                            <a href="<?= base_url('passwords/create') ?>" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                                <i class="ti ti-lock"></i> Crear Contraseña
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- VISTA ESCRITORIO: TABLA (d-none d-md-block) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle text-nowrap mb-0 table-hover-custom" id="table-recent-shares">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-muted fs-3">Recurso Transferido</h6></th>
                                    <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-muted fs-3">Destinatario</h6></th>
                                    <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0 text-muted fs-3">Fecha de Envío</h6></th>
                                    <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0 text-muted fs-3">Estado</h6></th>
                                    <th class="border-bottom-0 text-end"><h6 class="fw-semibold mb-0 text-muted fs-3">Acciones</h6></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentShares as $log): ?>
                                    <?php 
                                        $isFileType = ($log->resource_type === 'file');
                                        $searchString = strtolower($log->resource_name . ' ' . $log->recipient_email . ' ' . ($isFileType ? 'archivo file' : 'contraseña password') . ' ' . ($log->status === 'success' ? 'entregado enviado success' : 'error failed'));
                                    ?>
                                    <tr class="table-row-hover <?= !empty($log->resource_id) ? 'cursor-pointer' : '' ?>" data-search-content="<?= esc($searchString) ?>" <?= !empty($log->resource_id) ? 'onclick="window.location=\'' . base_url(($isFileType ? 'files/edit/' : 'passwords/edit/') . $log->resource_id) . '\'"' : '' ?>>
                                        <!-- Recurso -->
                                        <td class="border-bottom-0">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 bg-light-primary text-primary rounded-3 d-flex align-items-center justify-content-center network-icon-circle">
                                                    <i class="ti <?= $isFileType ? 'ti-file-text' : 'ti-lock-access' ?> fs-6"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-truncate dashboard-item-title" title="<?= esc($log->resource_name) ?>">
                                                        <?= esc($log->resource_name) ?>
                                                    </h6>
                                                    <span class="fs-2 text-muted d-flex align-items-center gap-1 mt-1">
                                                        <i class="ti <?= $isFileType ? 'ti-folder' : 'ti-shield-lock' ?> fs-2"></i>
                                                        <?= $isFileType ? 'Archivo compartido' : 'Contraseña protegida' ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Destinatario -->
                                        <td class="border-bottom-0">
                                            <span class="fs-3"><?= esc($log->recipient_email) ?></span>
                                        </td>

                                        <!-- Fecha -->
                                        <td class="border-bottom-0 text-center">
                                            <h6 class="fs-3 fw-semibold mb-0"><?= date('d/m/Y', strtotime($log->created_at)) ?></h6>
                                            <span class="fw-normal text-muted fs-2 text-login-time d-inline-flex align-items-center gap-1">
                                                <i class="ti ti-clock fs-2"></i><?= date('H:i', strtotime($log->created_at)) ?>
                                            </span>
                                        </td>

                                        <!-- Estado -->
                                        <td class="border-bottom-0 text-center">
                                            <?php if ($log->status === 'success'): ?>
                                                <span class="status-pill status-success">
                                                    <i class="ti ti-circle-check fs-3"></i>
                                                    <span>Entregado</span>
                                                </span>
                                            <?php else: ?>
                                                <span class="status-pill status-danger">
                                                    <i class="ti ti-alert-triangle fs-3"></i>
                                                    <span>Error</span>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Acciones (Menú de tres puntos) -->
                                        <td class="border-bottom-0 text-end" onclick="event.stopPropagation();">
                                            <div class="dropdown">
                                                <button class="btn btn-sm bg-transparent border-0 text-muted shadow-none p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper-config='{"strategy":"fixed"}'>
                                                    <i class="ti ti-dots-vertical fs-5"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <?php if ($isFileType && !empty($log->resource_id)): ?>
                                                        <li>
                                                            <a href="<?= base_url('files/download/' . $log->resource_id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="ti ti-download fs-5"></i> Descargar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="<?= base_url('files/edit/' . $log->resource_id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="ti ti-pencil fs-5"></i> Editar
                                                            </a>
                                                        </li>
                                                        <?php if (!empty($log->resource_slug)): ?>
                                                            <li>
                                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="navigator.clipboard.writeText('<?= base_url('s/' . $log->resource_slug) ?>'); if(window.systemAlertToast) { window.systemAlertToast.fire({ icon: 'success', title: 'Enlace copiado al portapapeles' }); }">
                                                                    <i class="ti ti-link fs-5"></i> Copiar Enlace
                                                                </button>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php elseif (!$isFileType && !empty($log->resource_id)): ?>
                                                        <li>
                                                            <a href="<?= base_url('passwords/edit/' . $log->resource_id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="ti ti-pencil fs-5"></i> Editar Ajustes
                                                            </a>
                                                        </li>
                                                        <?php if (!empty($log->resource_slug)): ?>
                                                            <li>
                                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="navigator.clipboard.writeText('<?= base_url('pwd/' . $log->resource_slug) ?>'); if(window.systemAlertToast) { window.systemAlertToast.fire({ icon: 'success', title: 'Enlace copiado al portapapeles' }); }">
                                                                    <i class="ti ti-link fs-5"></i> Copiar Enlace
                                                                </button>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <li>
                                                            <a href="<?= base_url($isFileType ? 'files' : 'passwords') ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="ti <?= $isFileType ? 'ti-folders' : 'ti-lock' ?> fs-5"></i> Ver en <?= $isFileType ? 'Archivos' : 'Contraseñas' ?>
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- Fila para cuando la búsqueda no produce resultados -->
                                <tr id="no-search-results" class="d-none">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-search-off fs-7 d-block mb-2 text-muted"></i>
                                            <h6 class="fw-semibold mb-1">Sin coincidencias</h6>
                                            <p class="fs-2 mb-0">No se encontraron envíos que coincidan con tu búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- VISTA MÓVIL: TARJETAS (d-block d-md-none - Patrón Deca) -->
                    <div id="recentSharesCardsContainer" class="d-block d-md-none">
                        <?php foreach ($recentShares as $log): ?>
                            <?php 
                                $isFileType = ($log->resource_type === 'file');
                                $searchString = strtolower($log->resource_name . ' ' . $log->recipient_email . ' ' . ($isFileType ? 'archivo file' : 'contraseña password') . ' ' . ($log->status === 'success' ? 'entregado enviado success' : 'error failed'));
                            ?>
                            <div class="card border shadow-sm mb-3 recent-share-card-item <?= !empty($log->resource_id) ? 'cursor-pointer' : '' ?>" data-search-content="<?= esc($searchString) ?>" <?= !empty($log->resource_id) ? 'onclick="window.location=\'' . base_url(($isFileType ? 'files/edit/' : 'passwords/edit/') . $log->resource_id) . '\'"' : '' ?>>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-start gap-2 flex-grow-1 min-w-0 pe-2">
                                            <div class="round-32 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-4 flex-shrink-0 mt-1">
                                                <i class="ti <?= $isFileType ? 'ti-file-text' : 'ti-lock-access' ?>"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <strong class="d-block text-body dashboard-card-title" title="<?= esc($log->resource_name) ?>">
                                                    <?= esc($log->resource_name) ?>
                                                </strong>
                                                <span class="fs-2 text-muted d-flex align-items-center gap-1 mt-1">
                                                    <i class="ti <?= $isFileType ? 'ti-folder' : 'ti-shield-lock' ?> fs-2"></i>
                                                    <?= $isFileType ? 'Archivo compartido' : 'Contraseña protegida' ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center flex-shrink-0" onclick="event.stopPropagation();">
                                            <div class="dropdown">
                                                <button class="btn btn-sm bg-transparent border-0 text-muted shadow-none p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper-config='{"strategy":"fixed"}'>
                                                    <i class="ti ti-dots-vertical fs-5"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <?php if ($isFileType && !empty($log->resource_id)): ?>
                                                        <li>
                                                            <a href="<?= base_url('files/download/' . $log->resource_id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="ti ti-download fs-5"></i> Descargar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="<?= base_url('files/edit/' . $log->resource_id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="ti ti-pencil fs-5"></i> Editar
                                                            </a>
                                                        </li>
                                                        <?php if (!empty($log->resource_slug)): ?>
                                                            <li>
                                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="navigator.clipboard.writeText('<?= base_url('s/' . $log->resource_slug) ?>'); if(window.systemAlertToast) { window.systemAlertToast.fire({ icon: 'success', title: 'Enlace copiado al portapapeles' }); }">
                                                                    <i class="ti ti-link fs-5"></i> Copiar Enlace
                                                                </button>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php elseif (!$isFileType && !empty($log->resource_id)): ?>
                                                        <li>
                                                            <a href="<?= base_url('passwords/edit/' . $log->resource_id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="ti ti-pencil fs-5"></i> Editar Ajustes
                                                            </a>
                                                        </li>
                                                        <?php if (!empty($log->resource_slug)): ?>
                                                            <li>
                                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="navigator.clipboard.writeText('<?= base_url('pwd/' . $log->resource_slug) ?>'); if(window.systemAlertToast) { window.systemAlertToast.fire({ icon: 'success', title: 'Enlace copiado al portapapeles' }); }">
                                                                    <i class="ti ti-link fs-5"></i> Copiar Enlace
                                                                </button>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <li>
                                                            <a href="<?= base_url($isFileType ? 'files' : 'passwords') ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="ti <?= $isFileType ? 'ti-folders' : 'ti-lock' ?> fs-5"></i> Ver en <?= $isFileType ? 'Archivos' : 'Contraseñas' ?>
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-muted small">
                                        <div><strong>Destinatario:</strong> <?= esc($log->recipient_email) ?></div>
                                        <div><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($log->created_at)) ?></div>
                                    </div>

                                    <div class="mt-2 pt-1">
                                        <?php if ($log->status === 'success'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle card-badge-mobile">
                                                <i class="ti ti-circle-check me-1"></i>Entregado
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle card-badge-mobile">
                                                <i class="ti ti-alert-triangle me-1"></i>Error
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div id="no-search-results-mobile" class="text-center py-4 d-none text-muted">
                            <i class="ti ti-search-off fs-7 d-block mb-2 text-muted opacity-75"></i>
                            <h6 class="fw-semibold mb-1">Sin coincidencias</h6>
                            <p class="fs-2 mb-0">No se encontraron envíos que coincidan con tu búsqueda.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Buscador en tiempo real optimizado (escritorio y móvil)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-logs');
    const clearBtn = document.getElementById('clear-search-logs');
    const noResultsRow = document.getElementById('no-search-results');
    const noResultsMobile = document.getElementById('no-search-results-mobile');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('table#table-recent-shares tbody tr.table-row-hover');
            const cards = document.querySelectorAll('#recentSharesCardsContainer .recent-share-card-item');
            let visibleCount = 0;
            
            if (clearBtn) {
                clearBtn.classList.toggle('d-none', filter.length === 0);
            }

            rows.forEach(row => {
                const searchContent = row.getAttribute('data-search-content') || '';
                if (filter === '' || searchContent.includes(filter)) {
                    row.classList.remove('d-none');
                    visibleCount++;
                } else {
                    row.classList.add('d-none');
                }
            });

            cards.forEach(card => {
                const searchContent = card.getAttribute('data-search-content') || '';
                if (filter === '' || searchContent.includes(filter)) {
                    card.classList.remove('d-none');
                } else {
                    card.classList.add('d-none');
                }
            });

            if (noResultsRow) {
                noResultsRow.classList.toggle('d-none', visibleCount > 0 || filter === '');
            }
            if (noResultsMobile) {
                noResultsMobile.classList.toggle('d-none', visibleCount > 0 || filter === '');
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }
    }
});
</script>

