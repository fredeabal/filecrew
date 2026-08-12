<!-- =====================================================================
     CABECERA Y BREADCRUMB
     ===================================================================== -->
<div class="card border shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-12 text-center text-md-start">
                <h4 class="fw-semibold mb-2 mb-md-8">Compartir Contraseña</h4>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center justify-content-md-start">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="<?= base_url('passwords') ?>">Contraseñas</a></li>
                        <li class="breadcrumb-item text-muted" aria-current="page">Nueva Contraseña</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     FORMULARIO DE CREACIÓN
     ===================================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <form action="<?= base_url('passwords/store') ?>" method="POST" id="password-form">
                    <?= csrf_field() ?>

                    <!-- Contenido de la Contraseña -->
                    <div class="mb-5">
                        <h5 class="fw-semibold mb-3">Contenido de la Contraseña</h5>
                        
                        <div class="mb-4">
                            <label for="password_content" class="form-label text-muted d-flex justify-content-between">
                                <span>Escribe o pega la contraseña/texto aquí</span>
                                <button type="button" class="btn btn-sm text-primaryp-0 bg-transparent border-0 d-flex align-items-center gap-1" id="btn-copy-password">
                                    <i class="ti ti-copy"></i> Copiar
                                </button>
                            </label>
                            <textarea name="password_content" id="password_content" class="form-control" rows="4" placeholder="El texto que introduzcas aquí será cifrado de forma segura..." required></textarea>
                        </div>
                        
                        <!-- Generador de Contraseñas -->
                        <div class="bg-light-primary rounded p-3 mb-4 border d-flex align-items-center justify-content-between gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-3 flex-grow-1">
                                <label for="pwd_length" class="form-label mb-0 text-muted text-nowrap d-flex align-items-center gap-2">
                                    <i class="ti ti-wand text-primary"></i> 
                                    Longitud: <strong id="pwd_length_val" class="text-primary fs-4">16</strong>
                                </label>
                                <input type="range" class="form-range flex-grow-1" id="pwd_length" min="8" max="64" value="16" style="max-width: 300px;">
                            </div>
                            <button type="button" class="btn btn-primary btn-sm px-3" id="btn-generate">
                                Generar
                            </button>
                        </div>
                    </div>

                    <!-- Ajustes de Seguridad y Expiración -->
                    <h5 class="fw-semibold mb-3">Opciones para compartir</h5>
                    
                    <div class="row mb-3">
                        <!-- Título -->
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label fw-semibold">Nombre de la Contraseña</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                <span class="text-primary ps-3 pe-2"><i class="ti ti-text-caption"></i></span>
                                <input type="text" name="title" id="title" placeholder="Ej: Credenciales del correo" class="px-1 py-2 input-transparent">
                            </div>
                            <small class="form-text text-muted mt-1 text-xs">Para identificarlo en tu panel. No será visible públicamente.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Custom Slug -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="custom_slug" class="form-label fw-semibold">Enlace Personalizado (Opcional)</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                <span class="text-primary ps-3 pe-1 text-nowrap"><?= base_url('pwd/') ?></span>
                                <input type="text" name="custom_slug" id="custom_slug" placeholder="mi-contraseña" class="px-1 py-2 input-transparent">
                            </div>
                            <small class="form-text text-muted mt-1 text-xs">Dejar vacío para generar uno aleatorio.</small>
                        </div>

                        <!-- Contraseña -->
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">PIN de acceso (Opcional)</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                <span class="text-primary ps-3 pe-2"><i class="ti ti-lock"></i></span>
                                <input type="password" name="password" id="password" placeholder="Establecer PIN" class="px-1 py-2 input-transparent">
                                <button class="btn border-0 bg-transparent text-muted px-3" type="button" id="toggle-password">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <!-- Caducidad en fecha -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="expires_at" class="form-label fw-semibold">Fecha de Caducidad (Opcional)</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden datepicker">
                                <span class="text-primary ps-3 pe-2 cursor-pointer" data-toggle><i class="ti ti-calendar-time"></i></span>
                                <input type="text" name="expires_at" id="expires_at" placeholder="Seleccionar fecha" autocomplete="off" data-input class="px-1 py-2 input-transparent">
                            </div>
                        </div>

                        <!-- Límite de vistas -->
                        <div class="col-md-6">
                            <label for="view_limit" class="form-label fw-semibold">Límite máximo de visualizaciones (Opcional)</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                <span class="text-primary ps-3 pe-2"><i class="ti ti-eye"></i></span>
                                <input type="number" name="view_limit" id="view_limit" min="1" placeholder="Ej: 1 (Para autodestrucción inmediata)" class="px-1 py-2 input-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Autodestrucción -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="auto_destroy" name="auto_destroy" value="1" checked onchange="document.getElementById('auto_destroy_warning').classList.toggle('d-none', !this.checked)">
                                <label class="form-check-label fw-semibold" for="auto_destroy">Autodestrucción</label>
                            </div>
                            <div id="auto_destroy_warning" class="text-primary mt-2 small">
                                <i class="ti ti-info-circle me-1"></i>El registro cifrado se eliminará permanentemente del servidor al caducar o alcanzar su límite.
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="d-flex justify-content-center mt-4">
                        <a href="<?= base_url('passwords') ?>" class="btn btn-danger px-4 me-2">
                            <i class="ti ti-x me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-shield-lock me-1"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Password Generator
    const btnGenerate = document.getElementById('btn-generate');
    const passwordContent = document.getElementById('password_content');
    const lengthRange = document.getElementById('pwd_length');
    const lengthVal = document.getElementById('pwd_length_val');

    lengthRange.addEventListener('input', function() {
        lengthVal.textContent = this.value;
    });
    
    btnGenerate.addEventListener('click', function() {
        const length = parseInt(lengthRange.value);
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=';
        let generated = '';
        for (let i = 0; i < length; i++) {
            generated += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        passwordContent.value = generated;
    });

    // 2. Copiar contraseña al portapapeles
    const btnCopySecret = document.getElementById('btn-copy-password');
    btnCopySecret.addEventListener('click', function() {
        if (!passwordContent.value) return;
        navigator.clipboard.writeText(passwordContent.value).then(() => {
            const originalHtml = this.innerHTML;
            this.innerHTML = '<i class="ti ti-check text-success"></i> Copiado';
            setTimeout(() => { this.innerHTML = originalHtml; }, 2000);
        });
    });

    // 3. Toggle password PIN visibility
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="ti ti-eye"></i>' : '<i class="ti ti-eye-off"></i>';
        });
    }

    // 4. Validar contenido
    const form = document.getElementById('password-form');
    form.addEventListener('submit', function(e) {
        if (!passwordContent.value.trim()) {
            e.preventDefault();
            if (window.systemAlert) {
                window.systemAlert.fire({ icon: 'warning', title: 'Falta contenido', html: '<div class="text-center">Por favor escribe o genera la contraseña que deseas compartir.</div>' });
            }
        }
    });
});
</script>
