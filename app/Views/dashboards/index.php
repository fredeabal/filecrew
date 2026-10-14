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
     TABLA DE ÚLTIMOS ARCHIVOS COMPARTIDOS
     ===================================================================== -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card dashboard-table-card shadow-none">
            <div class="card-body p-4">
                <!-- Barra superior: Título, Métricas y Filtros -->
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-light-primary text-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-files fs-6"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="card-title fw-semibold mb-0">Últimos Archivos Compartidos</h5>
                                <?php if (!empty($filesCount)): ?>
                                    <span class="badge bg-light-primary text-primary rounded-pill fs-2 px-2 py-1">
                                        <?= esc($filesCount) ?> <?= $filesCount === 1 ? 'archivo' : 'archivos' ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted fs-2">Tus archivos subidos más recientemente</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto search-form-responsive">
                        <!-- Buscador en tiempo real -->
                        <div class="position-relative search-box-container dashboard-search-wrap w-100 w-md-auto">
                            <input type="text" id="search-recent-files" class="form-control" placeholder="Buscar por nombre...">
                            <i class="ti ti-search search-icon text-muted"></i>
                            <button type="button" id="clear-search-recent" class="btn btn-sm border-0 position-absolute end-0 top-50 translate-middle-y text-muted d-none me-2" title="Limpiar búsqueda">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <a href="<?= base_url('files') ?>" class="btn btn-outline-primary d-inline-flex align-items-center gap-1 text-nowrap">
                            <i class="ti ti-folders"></i> Ver todos
                        </a>
                        <a href="<?= base_url('files/upload') ?>" class="btn btn-primary d-inline-flex align-items-center gap-1 text-nowrap">
                            <i class="ti ti-upload"></i> Subir
                        </a>
                    </div>
                </div>
                
                <?php if (empty($recentFiles)): ?>
                    <!-- Estado Vacío Ilustrado -->
                    <div class="text-center py-5">
                        <div class="d-inline-flex p-3 rounded-circle bg-light-primary text-primary mb-3">
                            <i class="ti ti-folder-off fs-8"></i>
                        </div>
                        <h5 class="fw-semibold mb-1">Sin archivos compartidos</h5>
                        <p class="text-muted mb-4 mx-auto dashboard-empty-desc">
                            Aún no has subido ningún archivo. Sube tu primer archivo para empezar a compartirlo de forma rápida y segura.
                        </p>
                        <a href="<?= base_url('files/upload') ?>" class="btn btn-primary d-inline-flex align-items-center gap-1">
                            <i class="ti ti-upload"></i> Subir Primer Archivo
                        </a>
                    </div>
                <?php else: ?>
                    <!-- VISTA ESCRITORIO: TABLA (d-none d-md-block) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle text-nowrap mb-0 table-hover-custom" id="table-recent-files">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-muted fs-3">Archivo</h6></th>
                                    <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0 text-muted fs-3">Tamaño</h6></th>
                                    <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0 text-muted fs-3">Creado</h6></th>
                                    <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0 text-muted fs-3">Descargas</h6></th>
                                    <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0 text-muted fs-3">Caducidad</h6></th>
                                    <th class="border-bottom-0 text-end"><h6 class="fw-semibold mb-0 text-muted fs-3">Acciones</h6></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentFiles as $file): ?>
                                    <?php 
                                        // Formatear tamaño de archivo
                                        $size = (float)$file->file_size;
                                        $units = ['B', 'KB', 'MB', 'GB'];
                                        $pow = floor(($size ? log($size) : 0) / log(1024));
                                        $pow = min($pow, count($units) - 1);
                                        $size /= pow(1024, $pow);
                                        $sizeFormatted = round($size, 2) . ' ' . $units[$pow];

                                        // Determinar si ha caducado
                                        $expired = false;
                                        if (!empty($file->expires_at) && strtotime($file->expires_at) < time()) {
                                            $expired = true;
                                        }
                                        if (!empty($file->download_limit) && $file->download_count >= $file->download_limit) {
                                            $expired = true;
                                        }

                                        $searchString = strtolower($file->filename);
                                    ?>
                                    <tr class="table-row-hover cursor-pointer" data-search-content="<?= esc($searchString) ?>" onclick="window.location='<?= base_url('files/edit/' . $file->id) ?>'">
                                        <!-- Archivo -->
                                        <td class="border-bottom-0">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 bg-light-primary text-primary rounded-3 d-flex align-items-center justify-content-center network-icon-circle">
                                                    <i class="ti <?= !empty($file->password) ? 'ti-lock' : 'ti-file-text' ?> fs-6"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-truncate dashboard-item-title" title="<?= esc($file->filename) ?>">
                                                        <?= esc($file->filename) ?>
                                                    </h6>
                                                    <span class="fs-2 text-muted d-flex align-items-center gap-1 mt-1">
                                                        <i class="ti <?= !empty($file->password) ? 'ti-lock' : 'ti-folder' ?> fs-2"></i>
                                                        <?= !empty($file->password) ? 'Protegido con PIN' : 'Archivo compartido' ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Tamaño -->
                                        <td class="border-bottom-0 text-center">
                                            <span class="fs-3"><?= $sizeFormatted ?></span>
                                        </td>

                                        <!-- Fecha Creación -->
                                        <td class="border-bottom-0 text-center">
                                            <h6 class="fs-3 fw-semibold mb-0"><?= date('d/m/Y', strtotime($file->created_at)) ?></h6>
                                            <span class="fw-normal text-muted fs-2 text-login-time d-inline-flex align-items-center gap-1">
                                                <i class="ti ti-clock fs-2"></i><?= date('H:i', strtotime($file->created_at)) ?>
                                            </span>
                                        </td>

                                        <!-- Descargas -->
                                        <td class="border-bottom-0 text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <span class="fw-semibold fs-3"><?= esc($file->download_count) ?></span>
                                                <span class="text-muted fs-2">/</span>
                                                <span class="text-muted fs-2"><?= !empty($file->download_limit) ? esc($file->download_limit) : '∞' ?></span>
                                            </div>
                                        </td>

                                        <!-- Estado / Caducidad -->
                                        <td class="border-bottom-0 text-center">
                                            <?php if ($expired): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-2 badge-status">Caducado</span>
                                            <?php elseif (!empty($file->expires_at)): ?>
                                                <h6 class="fs-3 fw-semibold mb-0"><?= date('d/m/Y', strtotime($file->expires_at)) ?></h6>
                                                <span class="fw-normal text-muted fs-2 text-login-time d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-clock fs-2"></i><?= date('H:i', strtotime($file->expires_at)) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-2 badge-status">Sin expiración</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Acciones -->
                                        <td class="border-bottom-0 text-end" onclick="event.stopPropagation();">
                                            <div class="dropdown">
                                                <button class="btn btn-sm bg-transparent border-0 text-muted shadow-none p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper-config='{"strategy":"fixed"}'>
                                                    <i class="ti ti-dots-vertical fs-5"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <a href="<?= base_url('files/download/' . $file->id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="ti ti-download fs-5"></i> Descargar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url('files/edit/' . $file->id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="ti ti-pencil fs-5"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="copyShareLink('<?= base_url('s/' . $file->slug) ?>')">
                                                            <i class="ti ti-link fs-5"></i> Copiar Enlace
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- Fila sin resultados de búsqueda -->
                                <tr id="no-search-results" class="d-none">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-search-off fs-7 d-block mb-2 text-muted"></i>
                                            <h6 class="fw-semibold mb-1">Sin coincidencias</h6>
                                            <p class="fs-2 mb-0">No se encontraron archivos que coincidan con tu búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- VISTA MÓVIL: TARJETAS (d-block d-md-none) -->
                    <div id="recentFilesCardsContainer" class="d-block d-md-none">
                        <?php foreach ($recentFiles as $file): ?>
                            <?php 
                                $size = (float)$file->file_size;
                                $units = ['B', 'KB', 'MB', 'GB'];
                                $pow = floor(($size ? log($size) : 0) / log(1024));
                                $pow = min($pow, count($units) - 1);
                                $size /= pow(1024, $pow);
                                $sizeFormatted = round($size, 2) . ' ' . $units[$pow];

                                $expired = false;
                                if (!empty($file->expires_at) && strtotime($file->expires_at) < time()) {
                                    $expired = true;
                                }
                                if (!empty($file->download_limit) && $file->download_count >= $file->download_limit) {
                                    $expired = true;
                                }

                                $searchString = strtolower($file->filename);
                            ?>
                            <div class="card border shadow-sm mb-3 recent-file-card-item cursor-pointer" data-search-content="<?= esc($searchString) ?>" onclick="window.location='<?= base_url('files/edit/' . $file->id) ?>'">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-start gap-2 flex-grow-1 min-w-0 pe-2">
                                            <div class="round-32 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-4 flex-shrink-0 mt-1">
                                                <i class="ti <?= !empty($file->password) ? 'ti-lock' : 'ti-file-text' ?>"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <strong class="d-block text-body dashboard-card-title text-truncate" title="<?= esc($file->filename) ?>">
                                                    <?= esc($file->filename) ?>
                                                </strong>
                                                <span class="fs-2 text-muted d-flex align-items-center gap-1 mt-1">
                                                    <i class="ti <?= !empty($file->password) ? 'ti-lock' : 'ti-folder' ?> fs-2"></i>
                                                    <?= !empty($file->password) ? 'Protegido con PIN' : 'Archivo compartido' ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center flex-shrink-0" onclick="event.stopPropagation();">
                                            <div class="dropdown">
                                                <button class="btn btn-sm bg-transparent border-0 text-muted shadow-none p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper-config='{"strategy":"fixed"}'>
                                                    <i class="ti ti-dots-vertical fs-5"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <a href="<?= base_url('files/download/' . $file->id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="ti ti-download fs-5"></i> Descargar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url('files/edit/' . $file->id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="ti ti-pencil fs-5"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="copyShareLink('<?= base_url('s/' . $file->slug) ?>')">
                                                            <i class="ti ti-link fs-5"></i> Copiar Enlace
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top">
                                        <div>
                                            <span><strong><?= $sizeFormatted ?></strong></span>
                                            <span class="mx-1">•</span>
                                            <span><?= esc($file->download_count) ?>/<?= !empty($file->download_limit) ? esc($file->download_limit) : '∞' ?> descargas</span>
                                        </div>
                                        <div>
                                            <?php if ($expired): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle card-badge-mobile">Caducado</span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle card-badge-mobile">Activo</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div id="no-search-results-mobile" class="text-center py-4 d-none text-muted">
                            <i class="ti ti-search-off fs-7 d-block mb-2 text-muted opacity-75"></i>
                            <h6 class="fw-semibold mb-1">Sin coincidencias</h6>
                            <p class="fs-2 mb-0">No se encontraron archivos que coincidan con tu búsqueda.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Copiar enlace al portapapeles
function copyShareLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        if (window.systemAlertToast) {
            window.systemAlertToast.fire({
                icon: 'success',
                title: 'Enlace copiado al portapapeles'
            });
        } else if (window.systemAlert) {
            window.systemAlert.fire({
                icon: 'success',
                title: '¡Enlace copiado!',
                html: '<div class="text-center">El enlace de compartición se ha guardado en tu portapapeles.</div>',
                iconColor: '#10B981'
            });
        }
    });
}

// Buscador en tiempo real optimizado (escritorio y móvil)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-recent-files');
    const clearBtn = document.getElementById('clear-search-recent');
    const noResultsRow = document.getElementById('no-search-results');
    const noResultsMobile = document.getElementById('no-search-results-mobile');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('table#table-recent-files tbody tr.table-row-hover');
            const cards = document.querySelectorAll('#recentFilesCardsContainer .recent-file-card-item');
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

