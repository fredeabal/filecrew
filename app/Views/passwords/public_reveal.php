        <div class="col-md-10 col-lg-8 col-xxl-6 auth-card">
            <div class="card mb-0 shadow-lg border-primary border-opacity-25 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                            <i class="ti ti-shield-check text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-1 text-primary">Contraseña Segura</h3>
                        <?php if ($destroyedNow): ?>
                            <div class="alert alert-danger mt-3 mb-0 border-0 shadow-sm" role="alert">
                                <i class="ti ti-alert-triangle me-2"></i><strong>Autodestruida:</strong> Cópiala ahora, desaparecerá si recargas.
                            </div>
                        <?php else: ?>
                            <p class="text-muted fs-3">Tu contraseña ha sido revelada con éxito.</p>
                        <?php endif; ?>
                    </div>

                    <!-- CAJA DEL SECRETO -->
                    <div class="mt-4">
                        <input type="text" 
                               readonly 
                               class="form-control text-center font-monospace fs-4 py-3 cursor-pointer" 
                               id="password-input" 
                               value="<?= esc($decryptedContent) ?>" 
                               title="Haz clic para copiar">
                    </div>

                </div>
            </div>
            
            <div class="text-center mt-4 fs-2 text-muted">
                Protegido por <strong>FileCrew</strong>
            </div>
        </div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById('password-input');
    
    if (passwordInput) {
        passwordInput.addEventListener('click', function() {
            this.select();
            const text = this.value;
            navigator.clipboard.writeText(text).then(() => {
                this.classList.add('password-input-copied');
                
                if (window.systemAlert) {
                    window.systemAlert.fire({
                        icon: 'success',
                        title: '¡Copiado!',
                        html: '<div class="text-center">La contraseña se ha guardado en tu portapapeles.</div>',
                        iconColor: '#10B981',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                
                setTimeout(() => { 
                    this.classList.remove('password-input-copied');
                }, 2000);
            });
        });
    }
});
</script>
