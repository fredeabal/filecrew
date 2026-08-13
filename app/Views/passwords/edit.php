<!-- =====================================================================
     CABECERA Y BREADCRUMB
     ===================================================================== -->
<div class="card border shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-12 text-center text-md-start">
                <h4 class="fw-semibold mb-2 mb-md-8">Editar Contraseña Compartida</h4>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center justify-content-md-start">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="<?= base_url('passwords') ?>">Contraseñas</a></li>
                        <li class="breadcrumb-item text-muted" aria-current="page">Editar Ajustes</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     FORMULARIO DE EDICIÓN
     ===================================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="alert-placeholder d-none"></div>

                    <form action="<?= base_url('passwords/update/' . $password->id) ?>" method="POST" id="edit-form">
                        <?= csrf_field() ?>

                        <!-- Nombre -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">Nombre</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                <span class="text-primary ps-3 pe-2"><i class="ti ti-text-caption"></i></span>
                                <input type="text" name="title" id="title" value="<?= esc($password->title) ?>" class="px-1 py-2 input-transparent">
                            </div>
                        </div>

                        <!-- Opciones para compartir -->
                        <div class="row mb-4">
                            <!-- Contraseña / PIN -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label for="password" class="form-label fw-semibold">
                                    PIN de acceso (Opcional) 
                                </label>
                                
                                <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                    <span class="text-primary ps-3 pe-2"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="password" id="password" placeholder="Establecer nuevo PIN (o dejar vacío)" class="px-1 py-2 input-transparent">
                                    <button class="btn border-0 bg-transparent text-muted px-3" type="button" id="toggle-password">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                                
                                <?php if (!empty($password->password)): ?>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_password" name="remove_password" value="1">
                                        <label class="form-check-label text-danger" for="remove_password">Eliminar protección por PIN actual</label>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Caducidad en fecha -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label for="expires_at" class="form-label fw-semibold">Fecha de Caducidad (Opcional)</label>
                                <?php 
                                    $expiresAtFormatted = '';
                                    if (!empty($password->expires_at)) {
                                        $expiresAtFormatted = date('d/m/Y', strtotime($password->expires_at));
                                    }
                                ?>
                                <div class="form-control p-0 d-flex align-items-center overflow-hidden datepicker">
                                    <span class="text-primary ps-3 pe-2 cursor-pointer" data-toggle><i class="ti ti-calendar-time"></i></span>
                                    <input type="text" name="expires_at" id="expires_at" value="<?= $expiresAtFormatted ?>" placeholder="Seleccionar fecha" autocomplete="off" data-input class="px-1 py-2 input-transparent">
                                </div>
                            </div>

                            <!-- Límite de vistas -->
                            <div class="col-md-4">
                                <label for="view_limit" class="form-label fw-semibold">
                                    Límite de visualizaciones (Opcional) 
                                </label>
                                <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                    <span class="text-primary ps-3 pe-2"><i class="ti ti-eye"></i></span>
                                    <input type="number" name="view_limit" id="view_limit" min="1" value="<?= esc($password->view_limit) ?>" placeholder="Dejar vacío para ilimitado" class="px-1 py-2 input-transparent">
                                </div>
                                <?php if (!empty($password->view_count)): ?>
                                    <small class="text-muted d-block mt-1">Visto: <?= esc($password->view_count) ?> veces</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Autodestrucción -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="auto_destroy" name="auto_destroy" value="1" <?= $password->auto_destroy ? 'checked' : '' ?> onchange="document.getElementById('auto_destroy_warning').classList.toggle('d-none', !this.checked)">
                                    <label class="form-check-label fw-semibold" for="auto_destroy">Autodestrucción</label>
                                </div>
                                <div id="auto_destroy_warning" class="text-primary mt-2 small <?= $password->auto_destroy ? '' : 'd-none' ?>">
                                    <i class="ti ti-info-circle me-1"></i>El registro cifrado se eliminará permanentemente de la base de datos al caducar o alcanzar su límite de vistas.
                                </div>
                            </div>
                        </div>

                    </form> <!-- Fin del formulario de edición -->

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-center align-items-center mt-5 gap-2">
                        <!-- Botón Cancelar -->
                        <a href="<?= base_url('passwords') ?>" class="btn btn-danger px-3 px-sm-4">
                            <i class="ti ti-x me-sm-1"></i><span class="d-none d-sm-inline">Cancelar</span>
                        </a>

                        <!-- Botón Borrar (Formulario propio) -->
                        <form action="<?= base_url('passwords/delete/' . $password->id) ?>" method="POST" class="m-0" data-confirm="Esta acción borrará físicamente la contraseña y caducará el enlace de compartición. ¿Deseas continuar?">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger px-3 px-sm-4">
                                <i class="ti ti-trash me-sm-1"></i><span class="d-none d-sm-inline">Borrar</span>
                            </button>
                        </form>

                        <!-- Botón Guardar (Envía el form de edición) -->
                        <button type="submit" form="edit-form" class="btn btn-primary px-3 px-sm-4">
                            <i class="ti ti-device-floppy me-sm-1"></i><span class="d-none d-sm-inline">Guardar</span>
                        </button>
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="ti ti-eye"></i>' : '<i class="ti ti-eye-off"></i>';
        });
    }
});

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
