<!-- =====================================================================
     CABECERA Y BREADCRUMB
     ===================================================================== -->
<div class="card border shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-12 text-center text-md-start">
                <h4 class="fw-semibold mb-2 mb-md-8">Compartir Archivos</h4>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center justify-content-md-start">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="<?= base_url('files') ?>">Archivos</a></li>
                        <li class="breadcrumb-item text-muted" aria-current="page">Subir</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     FORMULARIO DE SUBIDA (ESTILO WETRANSFER)
     ===================================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <form action="<?= base_url('files/store') ?>" method="POST" enctype="multipart/form-data" id="upload-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="temp_storage" id="temp_storage" value="">
                    <input type="hidden" name="original_filename" id="original_filename" value="">

                    <!-- Zona Drag & Drop Multi-archivo -->
                    <div class="mb-4">
                        <div class="upload-dropzone" id="dropzone">
                            <div class="upload-icon-wrapper mb-3">
                                <i class="ti ti-cloud-upload fs-7"></i>
                            </div>
                            <h5 class="fw-semibold mb-2">Arrastra tus archivos aquí</h5>
                            <p class="text-muted mb-3 fs-3">Puedes añadir uno o múltiples archivos a la vez</p>
                            
                            <!-- Input múltiple oculto según estándares del workspace -->
                            <input type="file" name="uploaded_files_proxy" id="uploaded_files_proxy" multiple class="d-none">
                            <label for="uploaded_files_proxy" class="btn btn-primary cursor-pointer px-4">
                                <i class="ti ti-plus me-1"></i> Seleccionar archivos
                            </label>
                        </div>
                        
                        <!-- Lista de archivos seleccionados (Estilo WeTransfer) -->
                        <div class="mt-3 d-none" id="files-list-container">
                            <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                                <span class="fw-semibold text-muted fs-3">
                                    <span id="files-count-badge">0</span> archivos seleccionados (<span id="files-total-size">0 MB</span>)
                                </span>
                                <div class="d-flex gap-2">
                                    <label for="uploaded_files_proxy" class="btn btn-sm btn-outline-primary cursor-pointer py-1 px-3">
                                        <i class="ti ti-plus"></i> Añadir más
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3" id="btn-clear-all-files">
                                        <i class="ti ti-trash"></i> Vaciar
                                    </button>
                                </div>
                            </div>
                            <div class="upload-files-list d-flex flex-column gap-2" id="files-items-wrapper">
                                <!-- Elementos inyectados dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <!-- Ajustes de Seguridad y Expiración -->
                    <h5 class="fw-semibold mb-3">Opciones de la transferencia</h5>
                    
                    <div class="row mb-3">
                        <!-- Nombre del Archivo / Paquete -->
                        <div class="col-md-12 mb-3">
                            <label for="filename" class="form-label fw-semibold">Nombre de la descarga</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                <span class="text-primary ps-3 pe-2"><i class="ti ti-file-description"></i></span>
                                <input type="text" name="filename" id="filename" placeholder="Nombre para tus archivos" class="px-1 py-2 input-transparent">
                                <span id="filename-ext" class="pe-3 fw-bold text-muted"></span>
                            </div>
                            <small class="form-text text-muted mt-1 text-xs">Si subes múltiples archivos se empaquetarán automáticamente en un archivo .zip.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Enlace Personalizado -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="custom_slug" class="form-label fw-semibold">Enlace Personalizado (Opcional)</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                <span class="text-primary ps-3 pe-1 text-nowrap"><?= base_url('s/') ?></span>
                                <input type="text" name="custom_slug" id="custom_slug" placeholder="mi-enlace" class="px-1 py-2 input-transparent">
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

                        <!-- Límite de descargas -->
                        <div class="col-md-6">
                            <label for="download_limit" class="form-label fw-semibold">Límite máximo de descargas (Opcional)</label>
                            <div class="form-control p-0 d-flex align-items-center overflow-hidden">
                                <span class="text-primary ps-3 pe-2"><i class="ti ti-download"></i></span>
                                <input type="number" name="download_limit" id="download_limit" min="1" placeholder="Dejar vacío para ilimitado" class="px-1 py-2 input-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Autodestrucción -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="auto_destroy" name="auto_destroy" value="1" onchange="document.getElementById('auto_destroy_warning').classList.toggle('d-none', !this.checked)">
                                <label class="form-check-label fw-semibold" for="auto_destroy">Autodestrucción</label>
                            </div>
                            <div id="auto_destroy_warning" class="text-primary mt-2 small d-none">
                                <i class="ti ti-info-circle me-1"></i>El archivo se borrará físicamente del servidor al caducar o alcanzar su límite.
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="d-flex justify-content-center mt-4">
                        <a href="<?= base_url('files') ?>" class="btn btn-danger px-4 me-2">
                            <i class="ti ti-x me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="btn-submit-transfer">
                            <i class="ti ti-send me-1"></i>Transferir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     MODAL DE PROGRESO DE SUBIDA EN TIEMPO REAL (CIRCULAR / WETRANSFER)
     ===================================================================== -->
<div class="upload-modal-backdrop d-none" id="upload-progress-modal">
    <div class="upload-progress-card text-center">
        <!-- Indicador circular -->
        <div class="circular-progress-wrap mb-4">
            <svg class="circular-progress-svg" viewBox="0 0 140 140">
                <circle class="circular-progress-bg" cx="70" cy="70" r="60"></circle>
                <circle class="circular-progress-bar" id="circular-bar" cx="70" cy="70" r="60"></circle>
            </svg>
            <div class="circular-progress-text" id="circular-percent">0%</div>
        </div>

        <h4 class="fw-bold mb-2" id="upload-status-title">Transfiriendo archivos...</h4>
        <p class="text-muted mb-3 fs-3" id="upload-status-desc">Preparando y fragmentando datos de forma segura...</p>

        <!-- Métricas de subida -->
        <div class="p-3 bg-light-primary rounded d-flex justify-content-around text-center mb-4">
            <div>
                <small class="text-muted d-block">Subido</small>
                <span class="fw-semibold text-primary" id="metric-uploaded">0 MB</span>
            </div>
            <div>
                <small class="text-muted d-block">Velocidad</small>
                <span class="fw-semibold text-primary" id="metric-speed">0 MB/s</span>
            </div>
            <div>
                <small class="text-muted d-block">Restante</small>
                <span class="fw-semibold text-primary" id="metric-time">-- s</span>
            </div>
        </div>

        <button type="button" class="btn btn-outline-danger px-4" id="btn-cancel-upload">
            <i class="ti ti-x me-1"></i> Cancelar transferencia
        </button>
    </div>
</div>

<!-- =====================================================================
     MODAL DE ÉXITO DE TRANSFERENCIA (ESTILO WETRANSFER / DARK & LIGHT PREMIUM)
     ===================================================================== -->
<div class="upload-modal-backdrop d-none" id="transfer-success-modal">
    <div class="upload-progress-card text-center p-4 p-md-5">
        <div class="transfer-success-icon-wrap mb-4">
            <i class="ti ti-check fs-9"></i>
        </div>

        <h3 class="fw-bolder mb-2">¡Transferencia Lista!</h3>
        <p class="text-muted mb-4 fs-3">Tus archivos se han subido con éxito y el enlace seguro está listo para compartir.</p>

        <!-- Caja de enlace con copiar -->
        <div class="transfer-link-box mb-4">
            <div class="transfer-link-label">Tu enlace de descarga</div>
            <div class="transfer-link-row">
                <i class="ti ti-link transfer-link-icon"></i>
                <a href="#" target="_blank" rel="noopener" class="transfer-link-text" id="success-share-link"></a>
                <button type="button" class="btn btn-primary transfer-btn" id="btn-copy-success-link">
                    <i class="ti ti-copy"></i><span>Copiar</span>
                </button>
            </div>
        </div>

        <!-- Acciones principales -->
        <div class="transfer-actions">
            <a href="<?= base_url('files') ?>" class="btn btn-primary transfer-btn">
                <i class="ti ti-list"></i><span>Ir a mis archivos</span>
            </a>
            <button type="button" class="btn transfer-btn-secondary transfer-btn" id="btn-upload-another">
                <i class="ti ti-plus"></i><span>Subir otro archivo</span>
            </button>
        </div>
    </div>
</div>

<!-- JSZip para empaquetado seguro local en transferencias múltiples -->
<script src="<?= base_url('assets/libs/jszip.min.js') ?>"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropzone = document.getElementById('dropzone');
    const proxyInput = document.getElementById('uploaded_files_proxy');
    const filesListContainer = document.getElementById('files-list-container');
    const filesItemsWrapper = document.getElementById('files-items-wrapper');
    const filesCountBadge = document.getElementById('files-count-badge');
    const filesTotalSize = document.getElementById('files-total-size');
    const btnClearAll = document.getElementById('btn-clear-all-files');
    const form = document.getElementById('upload-form');
    const filenameInput = document.getElementById('filename');
    const filenameExtSpan = document.getElementById('filename-ext');
    const tempStorageInput = document.getElementById('temp_storage');
    const originalFilenameInput = document.getElementById('original_filename');

    // Elementos del modal de progreso
    const progressModal = document.getElementById('upload-progress-modal');
    const circularBar = document.getElementById('circular-bar');
    const circularPercent = document.getElementById('circular-percent');
    const statusTitle = document.getElementById('upload-status-title');
    const statusDesc = document.getElementById('upload-status-desc');
    const metricUploaded = document.getElementById('metric-uploaded');
    const metricSpeed = document.getElementById('metric-speed');
    const metricTime = document.getElementById('metric-time');
    const btnCancelUpload = document.getElementById('btn-cancel-upload');

    // Longitud de la circunferencia: 2 * PI * 60 ~= 376.99
    const CIRCUMFERENCE = 377;
    let selectedFiles = [];
    let isUploading = false;
    let abortCurrentUpload = false;

    // 1. Manejo de selección de archivos
    proxyInput.addEventListener('change', function() {
        if (proxyInput.files.length > 0) {
            Array.from(proxyInput.files).forEach(file => {
                // Evitar duplicados exactos por nombre y tamaño
                const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    selectedFiles.push(file);
                }
            });
            proxyInput.value = '';
            renderFilesList();
        }
    });

    // 2. Drag and drop
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                dropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            }, false);
        });

        dropzone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            if (dt && dt.files.length > 0) {
                Array.from(dt.files).forEach(file => {
                    const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                    if (!exists) {
                        selectedFiles.push(file);
                    }
                });
                renderFilesList();
            }
        }, false);
    }

    // 3. Renderizar listado de archivos seleccionados
    function renderFilesList() {
        filesItemsWrapper.innerHTML = '';
        if (selectedFiles.length === 0) {
            filesListContainer.classList.add('d-none');
            filenameInput.value = '';
            filenameExtSpan.textContent = '';
            return;
        }

        filesListContainer.classList.remove('d-none');
        filesCountBadge.textContent = selectedFiles.length;

        let totalBytes = 0;
        selectedFiles.forEach((file, index) => {
            totalBytes += file.size;

            const item = document.createElement('div');
            item.className = 'upload-file-item rounded p-2 px-3 d-flex align-items-center justify-content-between';
            item.innerHTML = `
                <div class="upload-file-main">
                    <div class="upload-file-icon">
                        <i class="ti ti-file-text fs-5"></i>
                    </div>
                    <div class="upload-file-meta">
                        <span class="upload-file-name" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</span>
                        <span class="upload-file-size">${formatBytes(file.size)}</span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-outline-danger border-0" data-remove-index="${index}" title="Eliminar archivo">
                    <i class="ti ti-x"></i>
                </button>
            `;
            filesItemsWrapper.appendChild(item);
        });

        filesTotalSize.textContent = formatBytes(totalBytes);

        // Auto-asignar nombre por defecto
        if (selectedFiles.length === 1) {
            const single = selectedFiles[0];
            const lastDot = single.name.lastIndexOf('.');
            filenameInput.value = lastDot !== -1 ? single.name.substring(0, lastDot) : single.name;
            filenameExtSpan.textContent = lastDot !== -1 ? single.name.substring(lastDot) : '';
        } else {
            if (!filenameInput.value || filenameInput.value.endsWith('.zip')) {
                filenameInput.value = 'transferencia_' + new Date().toISOString().slice(0, 10);
            }
            filenameExtSpan.textContent = '.zip';
        }

        // Listener para eliminar individualmente
        document.querySelectorAll('[data-remove-index]').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-remove-index'));
                selectedFiles.splice(idx, 1);
                renderFilesList();
            });
        });
    }

    // Vaciar todo
    btnClearAll.addEventListener('click', function() {
        selectedFiles = [];
        renderFilesList();
    });

    // 4. Toggle contraseña
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="ti ti-eye"></i>' : '<i class="ti ti-eye-off"></i>';
    });

    // 5. Cancelar transferencia
    btnCancelUpload.addEventListener('click', function() {
        abortCurrentUpload = true;
        progressModal.classList.add('d-none');
        isUploading = false;
        if (window.systemAlert) {
            window.systemAlert.fire({ icon: 'info', title: 'Cancelado', html: '<div class="text-center">La subida ha sido cancelada.</div>', timer: 2000 });
        }
    });

    // 6. Proceso de subida con soporte para multi-archivos y Chunks
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (selectedFiles.length === 0) {
            if (window.systemAlert) {
                window.systemAlert.fire({ icon: 'warning', title: 'Archivos faltantes', html: '<div class="text-center">Por favor selecciona o arrastra al menos un archivo.</div>', iconColor: '#FFAE1F' });
            }
            return;
        }

        if (isUploading) return;
        isUploading = true;
        abortCurrentUpload = false;

        // Abrir modal de progreso
        progressModal.classList.remove('d-none');
        updateProgress(0, 0, 0, 0, 'Iniciando transferencia...');

        try {
            let finalBlobToUpload = null;
            let finalOriginalName = '';

            // Caso A: Si hay múltiples archivos, comprimir en ZIP con JSZip en el navegador
            if (selectedFiles.length > 1) {
                statusTitle.textContent = 'Comprimiendo archivos...';
                statusDesc.textContent = 'Empaquetando tus archivos en un único archivo comprimido .zip';
                
                const zip = new JSZip();
                selectedFiles.forEach(file => {
                    zip.file(file.name, file);
                });

                finalBlobToUpload = await zip.generateAsync({
                    type: 'blob',
                    compression: 'DEFLATE',
                    compressionOptions: { level: 6 }
                }, function(metadata) {
                    const percent = Math.round(metadata.percent);
                    updateProgress(percent, 0, 0, 0, `Comprimiendo: ${metadata.currentFile ? escapeHtml(metadata.currentFile) : ''}`);
                });

                finalOriginalName = (filenameInput.value || 'transferencia') + '.zip';
            } else {
                finalBlobToUpload = selectedFiles[0];
                finalOriginalName = selectedFiles[0].name;
            }

            if (abortCurrentUpload) return;

            // Caso B: Chunked Upload (Fragmentación en bloques de 2MB)
            const CHUNK_SIZE = 2 * 1024 * 1024; // 2 MB por fragmento (compatible con cualquier server PHP)
            const totalSize = finalBlobToUpload.size;
            const totalChunks = Math.ceil(totalSize / CHUNK_SIZE);
            const uploadId = 'up_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);

            statusTitle.textContent = 'Transfiriendo archivos...';
            statusDesc.textContent = `Subiendo ${totalChunks} fragmento${totalChunks > 1 ? 's' : ''} de alta velocidad...`;

            let tempStorageFileName = '';
            let bytesUploadedSoFar = 0;
            const startTime = Date.now();

            for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                if (abortCurrentUpload) return;

                const start = chunkIndex * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, totalSize);
                const chunkBlob = finalBlobToUpload.slice(start, end);

                const chunkData = new FormData();
                chunkData.append('chunk', chunkBlob, 'part_' + chunkIndex);
                chunkData.append('chunk_index', chunkIndex);
                chunkData.append('total_chunks', totalChunks);
                chunkData.append('upload_id', uploadId);

                // Obtener token CSRF
                const csrfToken = document.querySelector('input[name="csrf_test_name"]')?.value || '';
                chunkData.append('csrf_test_name', csrfToken);

                const chunkRes = await uploadSingleChunk(chunkData, (chunkBytes) => {
                    const currentTotalBytes = bytesUploadedSoFar + chunkBytes;
                    const percent = Math.min(99, Math.round((currentTotalBytes / totalSize) * 100));
                    const elapsedSec = (Date.now() - startTime) / 1000;
                    const speed = elapsedSec > 0 ? (currentTotalBytes / elapsedSec) : 0;
                    const remainingSec = speed > 0 ? Math.round((totalSize - currentTotalBytes) / speed) : 0;

                    updateProgress(percent, currentTotalBytes, speed, remainingSec, `Subiendo fragmento ${chunkIndex + 1} de ${totalChunks}...`);
                });

                bytesUploadedSoFar += (end - start);

                if (!chunkRes.success) {
                    throw new Error(chunkRes.error || 'Error durante la subida de fragmentos.');
                }

                if (chunkRes.completed && chunkRes.temp_storage) {
                    tempStorageFileName = chunkRes.temp_storage;
                }
            }

            if (abortCurrentUpload) return;

            // Finalizar registro de transferencia
            statusTitle.textContent = 'Finalizando transferencia...';
            statusDesc.textContent = 'Verificando y generando enlace seguro...';
            updateProgress(100, totalSize, 0, 0, 'Completando...');

            tempStorageInput.value = tempStorageFileName;
            originalFilenameInput.value = finalOriginalName;

            const finalFormPayload = new FormData(form);
            const storeRes = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: finalFormPayload
            });

            const result = await storeRes.json();

            if (result.success) {
                progressModal.classList.add('d-none');
                
                // Mostrar el modal nativo estilizado WeTransfer
                const successModal = document.getElementById('transfer-success-modal');
                const successInput = document.getElementById('success-share-link');
                const btnCopy = document.getElementById('btn-copy-success-link');
                const btnUploadAnother = document.getElementById('btn-upload-another');

                if (successModal && successInput) {
                    successInput.textContent = result.share_url;
                    successInput.href = result.share_url;
                    successModal.classList.remove('d-none');

                    btnCopy.onclick = function() {
                        navigator.clipboard.writeText(result.share_url);
                        btnCopy.innerHTML = '<i class="ti ti-check"></i><span>¡Copiado!</span>';
                        btnCopy.classList.add('is-copied');
                        setTimeout(() => {
                            btnCopy.innerHTML = '<i class="ti ti-copy"></i><span>Copiar</span>';
                            btnCopy.classList.remove('is-copied');
                        }, 2500);
                    };

                    btnUploadAnother.onclick = function() {
                        window.location.reload();
                    };
                } else {
                    window.location.href = '<?= base_url('files') ?>';
                }
            } else {
                throw new Error(result.error || (result.errors ? Object.values(result.errors).join('<br>') : 'Error al guardar la transferencia.'));
            }

        } catch (error) {
            progressModal.classList.add('d-none');
            isUploading = false;
            if (window.systemAlert) {
                window.systemAlert.fire({
                    icon: 'error',
                    title: 'Error de transferencia',
                    html: `<div class="text-center">${error.message || 'Ocurrió un error inesperado al procesar la subida.'}</div>`,
                    iconColor: '#b31b34'
                });
            } else {
                alert(error.message);
            }
        }
    });

    // Helper: Enviar un fragmento con XHR para monitorizar progreso
    function uploadSingleChunk(formData, onChunkProgress) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= base_url('files/upload-chunk') ?>', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable && onChunkProgress) {
                    onChunkProgress(e.loaded);
                }
            };

            xhr.onload = function() {
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(resp);
                    } else {
                        reject(new Error(resp.error || 'Fallo en fragmento con código ' + xhr.status));
                    }
                } catch (e) {
                    reject(new Error('Respuesta inválida del servidor.'));
                }
            };

            xhr.onerror = function() {
                reject(new Error('Error de conexión de red durante la subida.'));
            };

            xhr.send(formData);
        });
    }

    // Helper: Actualizar gráfica circular y métricas
    function updateProgress(percent, uploadedBytes, speedBytesPerSec, remainingSeconds, description) {
        circularPercent.textContent = percent + '%';
        const offset = CIRCUMFERENCE - (percent / 100) * CIRCUMFERENCE;
        circularBar.style.strokeDashoffset = offset;

        if (description) {
            statusDesc.textContent = description;
        }

        if (uploadedBytes > 0) {
            metricUploaded.textContent = formatBytes(uploadedBytes);
        }
        if (speedBytesPerSec > 0) {
            metricSpeed.textContent = formatBytes(speedBytesPerSec) + '/s';
        }
        if (remainingSeconds > 0) {
            metricTime.textContent = remainingSeconds > 60 ? Math.ceil(remainingSeconds / 60) + ' min' : remainingSeconds + ' s';
        } else if (percent === 100) {
            metricTime.textContent = '0 s';
        }
    }

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function escapeHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
});
</script>
