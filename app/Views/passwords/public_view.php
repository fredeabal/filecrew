        <div class="col-md-8 col-lg-6 col-xxl-4 auth-card">
            <div class="card mb-0">
                <div class="card-body">
                    <a href="<?= base_url() ?>" class="text-nowrap logo-img text-center d-block mb-5 w-100">
                        <img src="<?= base_url('assets/images/logos/dark-logo.svg') ?>" class="dark-logo" alt="Logo-Dark" />
                        <img src="<?= base_url('assets/images/logos/light-logo.svg') ?>" class="light-logo" alt="Logo-light" />
                    </a>

                    <?php if ($requiresPassword): ?>
                        <!-- =====================================================================
                             FORMULARIO DE DESBLOQUEO POR CONTRASEÑA
                             ===================================================================== -->
                        <h3 class="fw-bold mb-2 text-center text-primary">Contraseña Protegida</h3>
                        <p class="text-muted mb-4 fs-3 text-center">Introduce el PIN configurado para desbloquear esta contraseña.</p>

                        <form action="<?= base_url('pwd/' . $password->slug . '/verify') ?>" method="POST" id="verify-password-form">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <div class="input-group">
                                    <input type="password" class="form-control text-center" name="password" id="password" placeholder="PIN de acceso">
                                    <button class="btn bg-transparent border text-muted" type="button" id="toggle-password">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2 d-flex align-items-center justify-content-center gap-2">
                                <i class="ti ti-lock-open fs-5"></i> Desbloquear Contraseña
                            </button>
                        </form>

                    <?php else: ?>
                        <!-- =====================================================================
                             REVELAR SECRETO
                             ===================================================================== -->
                        <div class="text-center mb-4">
                            <div class="d-inline-block p-4 rounded-circle bg-light-primary text-primary mb-3">
                                <i class="ti ti-lock-access fs-9"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-primary">Tienes una contraseña</h3>
                            <p class="text-muted fs-3">
                                Al revelar la contraseña, quedará registrado. 
                                <?php if ($password->auto_destroy && !empty($password->view_limit) && $password->view_limit == 1): ?>
                                    <br><strong class="text-danger">Advertencia: Esta contraseña se autodestruirá inmediatamente después de leerla.</strong>
                                <?php endif; ?>
                            </p>
                        </div>

                         <div class="p-3 bg-light-primary rounded mb-4 text-start">
                            <div class="row fs-2">
                                <div class="col-6 mb-3">
                                    <span class="fw-semibold d-block text-dark">Límite de vistas:</span>
                                    <span class="text-muted"><?= !empty($password->view_limit) ? esc($password->view_limit) . ' vistas' : 'Ilimitado' ?></span>
                                </div>
                                <div class="col-6 mb-3">
                                    <span class="fw-semibold d-block text-dark">Vistas actuales:</span>
                                    <span class="text-muted"><?= esc($password->view_count) ?> vistas</span>
                                </div>
                                <div class="col-12">
                                    <span class="fw-semibold d-block text-dark">Caducidad:</span>
                                    <span class="text-muted"><?= !empty($password->expires_at) ? date('d/m/Y H:i', strtotime($password->expires_at)) : 'Nunca' ?></span>
                                </div>
                            </div>
                        </div>

                        <a href="<?= base_url('pwd/' . $password->slug . '/reveal') ?>" class="btn btn-primary w-100 py-8 mb-4 rounded-2 mt-4 d-flex align-items-center justify-content-center gap-2 fs-5">
                            <i class="ti ti-eye fs-6"></i> Revelar Contraseña
                        </a>
                    <?php endif; ?>

                </div>
            </div>
            
            <div class="text-center mt-3 fs-2 text-muted">
                Compartido de forma segura a través de <strong>FileCrew</strong>
            </div>
        </div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            } else {
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            }
        });
    }
});
</script>
