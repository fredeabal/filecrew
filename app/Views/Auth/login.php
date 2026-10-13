<!DOCTYPE html>
<html lang="es" dir="ltr" data-color-theme="Blue_Theme" data-layout="vertical">
<head>
  <script>
    (function() {
      var saved = localStorage.getItem('theme');
      var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-bs-theme', theme);
    })();
  </script>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Acceso al Sistema | FileCrew</title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/images/logos/favicon.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=' . time()) ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/libs/sweetalert2/dist/sweetalert2.min.css') ?>" />
</head>

<body>
  <div id="main-wrapper" class="auth-customizer-none">
    <div class="position-relative overflow-hidden min-vh-100 w-100">
      <div class="position-relative z-index-5">
        <div class="row min-vh-100 g-0">
          
          <!-- Columna izquierda: Panel violeta con red de partículas -->
          <div class="col-xl-7 col-xxl-8 d-none d-xl-flex flex-column justify-content-center align-items-center auth-brand-panel position-relative">
            <!-- Animación de fondo: Red de nodos interactiva (Particles.js) -->
            <div id="particles-js" class="position-absolute w-100 h-100 top-0 start-0 z-index-1"></div>

            <div class="px-5 position-relative z-index-2 text-center text-xl-start mx-auto w-100 auth-left-content">
              <div class="d-inline-flex p-3 rounded-circle bg-white bg-opacity-10 text-white mb-4 shadow-sm backdrop-blur">
                <i class="ti ti-share fs-8"></i>
              </div>
              <h1 class="text-white display-4 fw-bolder mb-4 auth-headline">
                Compartición de archivos segura, privada y bajo tu control
              </h1>
              <p class="text-white fs-5 mb-0 auth-subtitle">
                Comparte archivos y contraseñas con enlaces protegidos por PIN, fecha de caducidad, límite de descargas y autodestrucción.
              </p>
            </div>
          </div>

          <!-- Columna derecha: Formulario de Login -->
          <div class="col-xl-5 col-xxl-4 bg-body d-flex justify-content-center align-items-center p-4">
            <div class="auth-max-width w-100 col-sm-8 col-md-6 col-xl-9 px-4">
              
              <!-- Logo de la plataforma -->
              <a href="<?= base_url() ?>" class="text-nowrap logo-img d-inline-block mb-4 text-decoration-none">
                <img src="<?= base_url('assets/images/logos/dark-logo.svg') ?>" class="dark-logo app-logo login-logo img-fluid" alt="FileCrew" />
                <img src="<?= base_url('assets/images/logos/light-logo.svg') ?>" class="light-logo app-logo login-logo img-fluid" alt="FileCrew" />
              </a>

              <h2 class="mb-1 fs-7 fw-bolder">Acceder al Sistema</h2>
              <p class="mb-7 text-muted">Introduce tus credenciales para continuar</p>

              <form id="loginForm" action="<?= url_to('login') ?>" method="post" novalidate autocomplete="off">
                <?= csrf_field() ?>

                <div class="mb-3">
                  <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                  <input type="email" class="form-control" id="email" name="email" inputmode="email" autocomplete="email" placeholder="ejemplo@empresa.com" value="<?= old('email') ?>" autofocus>
                </div>

                <div class="mb-4">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <label for="password" class="form-label fw-semibold mb-0">Contraseña</label>
                    <a href="<?= url_to('magic-link') ?>" class="text-primary fs-2 text-decoration-none fw-medium">¿Olvidaste tu contraseña?</a>
                  </div>
                  <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" placeholder="••••••••">
                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" tabindex="-1" title="Mostrar u ocultar contraseña">
                      <i class="ti ti-eye fs-4"></i>
                    </button>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-8 mb-0 rounded-2 mt-4 fs-4 fw-semibold shadow-sm" id="btn-login-submit">
                  <i class="ti ti-login me-1"></i>Iniciar Sesión
                </button>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Scripts principales -->
  <script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/libs/sweetalert2/dist/sweetalert2.min.js') ?>"></script>
  <script src="<?= base_url('assets/libs/particles.js/particles.min.js') ?>"></script>

  <script>
    // Inicialización de Particles.js
    document.addEventListener("DOMContentLoaded", function() {
      if (typeof particlesJS !== 'undefined' && document.getElementById('particles-js')) {
        particlesJS("particles-js", {
          "particles": {
            "number": { "value": 70, "density": { "enable": true, "value_area": 800 } },
            "color": { "value": "#ffffff" },
            "shape": { "type": "circle" },
            "opacity": { "value": 0.5, "random": false },
            "size": { "value": 3, "random": true },
            "line_linked": {
              "enable": true,
              "distance": 150,
              "color": "#ffffff",
              "opacity": 0.35,
              "width": 1
            },
            "move": {
              "enable": true,
              "speed": 4,
              "direction": "none",
              "random": false,
              "straight": false,
              "out_mode": "out",
              "bounce": false
            }
          },
          "interactivity": {
            "detect_on": "canvas",
            "events": {
              "onhover": { "enable": true, "mode": "repulse" },
              "onclick": { "enable": true, "mode": "push" },
              "resize": true
            },
            "modes": {
              "repulse": { "distance": 160, "duration": 0.4 },
              "push": { "particles_nb": 4 }
            }
          },
          "retina_detect": true
        });
      }

      // Toggle visibilidad de contraseña
      document.querySelectorAll('.btn-toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var input = btn.closest('.input-group').querySelector('input');
          var icon = btn.querySelector('i');
          if (!input) return;
          if (input.type === 'password') {
            input.type = 'text';
            if (icon) { icon.classList.replace('ti-eye', 'ti-eye-off'); }
          } else {
            input.type = 'password';
            if (icon) { icon.classList.replace('ti-eye-off', 'ti-eye'); }
          }
        });
      });

      // Estado de carga en envío de formulario
      var loginForm = document.getElementById('loginForm');
      if (loginForm) {
        loginForm.addEventListener('submit', function() {
          var btn = document.getElementById('btn-login-submit');
          if (btn && !btn.disabled) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Iniciando sesión...';
          }
        });
      }

      // Notificaciones con SweetAlert2
      var toastMessage = <?= json_encode(session()->getFlashdata('message') ?? session()->getFlashdata('success')) ?>;
      var toastError = <?= json_encode(session()->getFlashdata('error')) ?>;
      var toastErrors = <?= json_encode(session()->getFlashdata('errors')) ?>;
      
      var isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
      
      var systemAlert = Swal.mixin({
        position: 'center',
        showConfirmButton: false,
        buttonsStyling: false,
        timer: 4500,
        timerProgressBar: true,
        background: isDark ? '#0b1114' : '#f8f9fa',
        color: isDark ? '#fff' : '#0b1114',
        showCloseButton: false
      });
      
      if (toastMessage) {
        systemAlert.fire({ icon: 'success', title: '¡Completado!', html: '<div class="text-center">' + toastMessage + '</div>', iconColor: '#10B981' });
      }
      if (toastError) {
        systemAlert.fire({ icon: 'error', title: 'Error', html: '<div class="text-center">' + toastError + '</div>', iconColor: '#b31b34' });
      }
      if (toastErrors) {
        var errorContent = typeof toastErrors === 'object' && toastErrors !== null
          ? (Array.isArray(toastErrors) ? toastErrors : Object.values(toastErrors)).join('<br>') 
          : toastErrors;
        systemAlert.fire({ icon: 'error', title: 'Error de Validación', html: '<div class="text-center">' + errorContent + '</div>', iconColor: '#b31b34' });
      }
    });
  </script>
</body>
</html>
