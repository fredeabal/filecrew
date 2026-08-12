<style>
    .password-title-truncate { max-width: 120px; }
    @media (min-width: 768px) { .password-title-truncate { max-width: 250px; } }
    @media (min-width: 1200px) { .password-title-truncate { max-width: 300px; } }
</style>

<!-- =====================================================================
     CABECERA Y BREADCRUMB
     ===================================================================== -->
<div class="card border shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-8 text-center text-md-start">
                <h4 class="fw-semibold mb-2 mb-md-8">Contraseñas Compartidas</h4>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center justify-content-md-start">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item text-muted" aria-current="page">Contraseñas</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-end align-items-center mt-3 mt-md-0">
                <a href="<?= base_url('passwords/create') ?>" class="btn btn-primary border-0 d-flex align-items-center gap-1">
                    <i class="ti ti-lock"></i>
                    <span>Compartir Contraseña</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     BUSCADOR Y TABLA DE CONTRASEÑAS
     ===================================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Barra de Herramientas superior: Buscador -->
                <div class="d-flex flex-wrap justify-content-end align-items-center mb-4 gap-3">
                    <form action="<?= base_url('passwords') ?>" method="GET" class="d-flex align-items-center gap-2 w-100 w-md-auto search-form-responsive ms-auto">
                        <div class="position-relative w-100 search-box-container">
                            <input type="text" class="form-control" name="q" placeholder="Buscar por nombre..." value="<?= esc($search ?? '') ?>">
                            <i class="ti ti-search search-icon text-muted"></i>
                        </div>
                    </form>
                </div>

            <!-- Tabla Premium -->
            <div class="table-responsive">
                <table class="table align-middle text-nowrap mb-0" id="passwords-table">
                    <thead>
                        <tr>
                            <th scope="col">Nombre de la Contraseña</th>
                            <th scope="col" class="text-center d-none d-lg-table-cell">Creado</th>
                            <th scope="col" class="text-center d-none d-md-table-cell">Vistas</th>
                            <th scope="col" class="text-center d-none d-lg-table-cell">Expiración</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($passwords)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="ti ti-key fs-10 d-block mb-2 text-muted"></i>
                                    <span class="fw-semibold text-muted">No se encontraron contraseñas compartidas.</span>                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($passwords as $password): ?>
                                <?php 
                                    // Determinar si ha caducado
                                    $expired = false;
                                    if (!empty($password->expires_at) && strtotime($password->expires_at) < time()) {
                                        $expired = true;
                                    }
                                    if (!empty($password->view_limit) && $password->view_count >= $password->view_limit) {
                                        $expired = true;
                                    }
                                ?>
                                <tr class="cursor-pointer" onclick="window.location='<?= base_url('passwords/edit/' . $password->id) ?>'">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="p-2 <?= !empty($password->password) ? 'bg-light-warning text-warning' : 'bg-light-primary text-primary' ?> rounded d-none d-sm-flex align-items-center justify-content-center network-icon-circle">
                                                <i class="ti <?= !empty($password->password) ? 'ti-lock-access' : 'ti-key' ?> fs-6"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-truncate password-title-truncate">
                                                    <?= esc($password->title) ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell">
                                        <h6 class="fs-3 fw-semibold mb-0"><?= date('d/m/Y', strtotime($password->created_at)) ?></h6>
                                        <span class="fw-normal text-muted text-login-time"><?= date('H:i', strtotime($password->created_at)) ?></span>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span class="fw-semibold"><?= esc($password->view_count) ?></span>
                                            <span class="text-muted">/</span>
                                            <span class="text-muted"><?= !empty($password->view_limit) ? esc($password->view_limit) : '∞' ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell">
                                        <?php 
                                            $expiresAt = !empty($password->expires_at) ? strtotime($password->expires_at) : null;
                                            
                                            if ($expired): 
                                        ?>
                                            <?php if ($expiresAt): ?>
                                                <h6 class="fs-3 fw-semibold mb-0 text-danger"><?= date('d/m/Y', $expiresAt) ?></h6>
                                                <span class="fw-normal text-danger text-login-time"><?= date('H:i', $expiresAt) ?></span>
                                            <?php else: ?>
                                                <h6 class="fs-3 fw-semibold mb-0 text-danger">Caducado</h6>
                                            <?php endif; ?>
                                        <?php elseif ($expiresAt): ?>
                                            <h6 class="fs-3 fw-semibold mb-0"><?= date('d/m/Y', $expiresAt) ?></h6>
                                            <span class="fw-normal text-muted text-login-time"><?= date('H:i', $expiresAt) ?></span>
                                        <?php else: ?>
                                            <h6 class="fs-3 fw-semibold mb-0 text-muted">Nunca</h6>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end" onclick="event.stopPropagation();">
                                        <div class="dropdown">
                                            <button class="btn btn-sm bg-transparent border-0 text-muted shadow-none p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper-config='{"strategy":"fixed"}'>
                                                <i class="ti ti-dots-vertical fs-5"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a href="<?= base_url('passwords/edit/' . $password->id) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                                        <i class="ti ti-pencil"></i> Editar Ajustes
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="copyShareLink('<?= base_url('pwd/' . $password->slug) ?>')">
                                                        <i class="ti ti-link"></i> Copiar Enlace
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 btn-send-email" 
                                                            data-id="<?= $password->id ?>" 
                                                            data-title="<?= esc($password->title ?: 'Contraseña sin título') ?>">
                                                        <i class="ti ti-mail"></i> Enviar por Correo
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="<?= base_url('passwords/delete/' . $password->id) ?>" method="POST" class="d-inline" data-confirm="Esta acción borrará físicamente la contraseña y caducará el enlace de compartición.">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger w-100 border-0 bg-transparent text-start">
                                                            <i class="ti ti-trash"></i> Eliminar
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

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                <?= $pager->links('passwords', 'default_full') ?>
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
                        <label class="form-label text-muted">Contraseña seleccionada</label>
                        <input type="text" class="form-control bg-light" id="selected-password-title" readonly>
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
    // 1. Lógica para configurar y abrir el modal de envío de email
    const emailModal = new bootstrap.Modal(document.getElementById('emailModal'));
    const emailForm = document.getElementById('emailForm');
    const selectedPasswordField = document.getElementById('selected-password-title');
    
    document.querySelectorAll('.btn-send-email').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            
            selectedPasswordField.value = title;
            emailForm.action = `<?= base_url('passwords/send-email') ?>/${id}`;
            emailModal.show();
        });
    });
});

// 2. Copiar enlace al portapapeles
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
