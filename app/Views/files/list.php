
<!-- =====================================================================
     CABECERA Y BREADCRUMB
     ===================================================================== -->
<div class="card border shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-8 text-center text-md-start">
                <h4 class="fw-semibold mb-2 mb-md-8">Archivos Compartidos</h4>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center justify-content-md-start">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item text-muted" aria-current="page">Archivos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-end align-items-center mt-3 mt-md-0">
                <a href="<?= base_url('files/upload') ?>" class="btn btn-primary border-0 d-flex align-items-center gap-1">
                    <i class="ti ti-upload"></i>
                    <span>Compartir Archivo</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     BUSCADOR Y TABLA DE ARCHIVOS
     ===================================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card dashboard-table-card shadow-none">
            <div class="card-body p-4">
                <!-- Barra de Herramientas superior: Buscador -->
                <div class="d-flex flex-wrap justify-content-end align-items-center mb-4 gap-3">
                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto search-form-responsive ms-auto">
                        <div class="position-relative w-100 search-box-container">
                            <input type="text" id="search-files" class="form-control" placeholder="Buscar por nombre...">
                            <i class="ti ti-search search-icon text-muted"></i>
                        </div>
                    </div>
                </div>

            <!-- VISTA ESCRITORIO: TABLA (d-none d-md-block) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table align-middle text-nowrap mb-0 table-hover" id="files-table">
                    <thead>
                        <tr>
                            <th scope="col">Archivo</th>
                            <th scope="col" class="text-center">Creado</th>
                            <th scope="col" class="text-center">Tamaño</th>
                            <th scope="col" class="text-center">Descargas</th>
                            <th scope="col" class="text-center">Caducidad</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($files)): ?>
                            <tr class="no-records-row">
                                <td colspan="6" class="text-center py-4">
                                    <i class="ti ti-folder-off fs-10 d-block mb-2 text-muted"></i>
                                    <span class="fw-semibold text-muted">No se encontraron archivos compartidos.</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($files as $file): ?>
                                <?php 
                                    // Formatear tamaño de archivo en la vista
                                    $size = $file->file_size;
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
                                ?>
                                <tr class="cursor-pointer" onclick="window.location='<?= base_url('files/edit/' . $file->id) ?>'">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="round-32 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-4 flex-shrink-0">
                                                <i class="ti <?= !empty($file->password) ? 'ti-lock' : 'ti-file-text' ?>"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-body text-truncate file-name-truncate" title="<?= esc($file->filename) ?>">
                                                    <?= esc($file->filename) ?>
                                                </strong>
                                                <span class="fs-2 text-muted d-flex align-items-center gap-1 mt-1">
                                                    <i class="ti <?= !empty($file->password) ? 'ti-lock' : 'ti-folder' ?> fs-2"></i>
                                                    <?= !empty($file->password) ? 'Protegido con PIN' : 'Archivo compartido' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <h6 class="fs-3 fw-semibold mb-0"><?= date('d/m/Y', strtotime($file->created_at)) ?></h6>
                                        <span class="fw-normal text-muted text-login-time"><?= date('H:i', strtotime($file->created_at)) ?></span>
                                    </td>
                                    <td class="text-center"><?= $sizeFormatted ?></td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span class="fw-semibold"><?= esc($file->download_count) ?></span>
                                            <span class="text-muted">/</span>
                                            <span class="text-muted"><?= !empty($file->download_limit) ? esc($file->download_limit) : '∞' ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($expired): ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-2">Caducado</span>
                                        <?php elseif (!empty($file->expires_at)): ?>
                                            <h6 class="fs-3 fw-semibold mb-0"><?= date('d/m/Y', strtotime($file->expires_at)) ?></h6>
                                            <span class="fw-normal text-muted text-login-time"><?= date('H:i', strtotime($file->expires_at)) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-2">Sin expiración</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end" onclick="event.stopPropagation();">
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
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 btn-send-email" 
                                                            data-id="<?= $file->id ?>" 
                                                            data-filename="<?= esc($file->filename) ?>">
                                                        <i class="ti ti-mail fs-5"></i> Enviar por Correo
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="<?= base_url('files/delete/' . $file->id) ?>" method="POST" class="d-inline" data-confirm="Esta acción borrará físicamente el archivo del servidor y caducará el enlace de compartición.">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger w-100 border-0 bg-transparent text-start">
                                                            <i class="ti ti-trash fs-5"></i> Eliminar
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- VISTA MÓVIL: TARJETAS (d-block d-md-none - Patrón Deca) -->
            <div id="filesCardsContainer" class="d-block d-md-none">
                <?php if (empty($files)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-folder-off fs-10 d-block mb-2 text-muted opacity-75"></i>
                        <span>No se encontraron archivos compartidos.</span>
                    </div>
                <?php else: ?>
                    <?php foreach ($files as $file): ?>
                        <?php 
                            // Formatear tamaño de archivo en la vista
                            $size = $file->file_size;
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
                        ?>
                        <div class="card border shadow-sm mb-3 file-card-item cursor-pointer" onclick="window.location.href='<?= base_url('files/edit/' . $file->id) ?>'">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-start gap-2 flex-grow-1 min-w-0 pe-2">
                                        <div class="round-32 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-4 flex-shrink-0 mt-1">
                                            <i class="ti <?= !empty($file->password) ? 'ti-lock' : 'ti-file-text' ?>"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <strong class="d-block text-body file-card-title" title="<?= esc($file->filename) ?>">
                                                <?= esc($file->filename) ?>
                                            </strong>
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
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 btn-send-email" 
                                                            data-id="<?= $file->id ?>" 
                                                            data-filename="<?= esc($file->filename) ?>">
                                                        <i class="ti ti-mail fs-5"></i> Enviar por Correo
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="<?= base_url('files/delete/' . $file->id) ?>" method="POST" class="d-inline" data-confirm="Esta acción borrará físicamente el archivo del servidor y caducará el enlace de compartición.">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger w-100 border-0 bg-transparent text-start">
                                                            <i class="ti ti-trash fs-5"></i> Eliminar
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-muted small">
                                    <div><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($file->created_at)) ?></div>
                                    <div><strong>Tamaño:</strong> <?= $sizeFormatted ?></div>
                                    <div><strong>Descargas:</strong> <?= esc($file->download_count) ?> / <?= !empty($file->download_limit) ? esc($file->download_limit) : '∞' ?></div>
                                    <div><strong>Caducidad:</strong> <?= !empty($file->expires_at) ? date('d/m/Y H:i', strtotime($file->expires_at)) : 'Nunca' ?></div>
                                </div>

                                <div class="mt-2 pt-1 d-flex justify-content-end">
                                    <?php if ($expired): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle card-badge-mobile">Caducado</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle card-badge-mobile">Activo</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                <?= $pager->links('files', 'default_full') ?>
            </div>
        </div>
    </div>
</div>
<!-- =====================================================================
     MODAL PARA ENVIAR CORREO
     ===================================================================== -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="emailModalLabel">Enviar Enlace por Correo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="emailForm" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Archivo seleccionado</label>
                        <input type="text" class="form-control bg-light" id="selected-file-name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="recipient_email" class="form-label">Correo electrónico del destinatario</label>
                        <input type="email" class="form-control" name="recipient_email" id="recipient_email" placeholder="ejemplo@correo.com">
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-mail me-1"></i> Enviar Correo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Buscador en tiempo real (lado del cliente - escritorio y móvil)
    const searchInput = document.getElementById('search-files');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase().trim();
            
            // Filtrar tabla escritorio
            document.querySelectorAll('#files-table tbody tr:not(.no-records-row)').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });

            // Filtrar tarjetas móvil
            document.querySelectorAll('#filesCardsContainer .file-card-item').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // 1. Lógica para configurar y abrir el modal de envío de email
    const emailModal = new bootstrap.Modal(document.getElementById('emailModal'));
    const emailForm = document.getElementById('emailForm');
    const selectedFileField = document.getElementById('selected-file-name');
    
    document.querySelectorAll('.btn-send-email').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const filename = this.getAttribute('data-filename');
            
            selectedFileField.value = filename;
            emailForm.action = `<?= base_url('files/send-email') ?>/${id}`;
            emailModal.show();
        });
    });
});

// 3. Copiar enlace al portapapeles
function copyShareLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        if (window.systemAlert) {
            window.systemAlert.fire({
                icon: 'success',
                title: '¡Enlace copiado!',
                html: '<div class="text-center">El enlace de compartición se ha guardado en tu portapapeles.</div>',
                iconColor: '#10B981'
            });
        }
    });
}
</script>
