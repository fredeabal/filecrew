<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FileShareModel;
use CodeIgniter\I18n\Time;

class FileShareController extends BaseController
{
    protected $fileShareModel;
    protected $perPage = 15;

    public function __construct()
    {
        $this->fileShareModel = new FileShareModel();
    }

    // ---------------------------------------------------------------------
    // Listado de archivos compartidos por el usuario actual
    // ---------------------------------------------------------------------
    public function index()
    {
        // Limpiar archivos vencidos con autodestrucción (Ejecución silenciosa)
        $this->cleanupExpiredFiles();

        $userId = auth()->id();
        $query = $this->fileShareModel->where('user_id', $userId);

        $files = $query->orderBy('created_at', 'DESC')->paginate($this->perPage, 'files');
        $pager = $this->fileShareModel->pager;

        $data = [
            'title' => 'Archivos',
            'files' => $files,
            'pager' => $pager
        ];

        echo view('template/header', $data);
        echo view('files/list', $data);
        echo view('template/footer');
    }

    // ---------------------------------------------------------------------
    // Mostrar formulario de subida de archivos
    // ---------------------------------------------------------------------
    public function upload()
    {
        $data = [
            'title' => 'Compartir Archivo'
        ];

        echo view('template/header', $data);
        echo view('files/upload', $data);
        echo view('template/footer');
    }

    // ---------------------------------------------------------------------
    // Subida por fragmentos (Chunked Upload) para grandes archivos
    // ---------------------------------------------------------------------
    public function uploadChunk()
    {
        $userId = auth()->id();
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $chunkIndex = (int)$this->request->getPost('chunk_index');
        $totalChunks = (int)$this->request->getPost('total_chunks');
        $uploadId = preg_replace('/[^a-zA-Z0-9_-]/', '', $this->request->getPost('upload_id') ?? '');
        $fileChunk = $this->request->getFile('chunk');

        if (empty($uploadId) || !$fileChunk || !$fileChunk->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false, 
                'error' => 'Fragmento de archivo o identificador no válido.'
            ]);
        }

        $tempDir = WRITEPATH . 'uploads/chunks/' . $uploadId;
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $chunkFile = $tempDir . '/chunk_' . $chunkIndex;
        if (file_exists($chunkFile)) {
            unlink($chunkFile);
        }

        if (!$fileChunk->move($tempDir, 'chunk_' . $chunkIndex)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false, 
                'error' => 'Error al almacenar el fragmento ' . $chunkIndex
            ]);
        }

        // Si se han subido todos los fragmentos, unirlos en un único archivo
        if ($chunkIndex === ($totalChunks - 1)) {
            $finalStorageName = bin2hex(random_bytes(16)) . '.tmp';
            $finalPath = WRITEPATH . 'uploads/chunks/' . $finalStorageName;
            $out = fopen($finalPath, 'wb');

            if ($out === false) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false, 
                    'error' => 'Error al ensamblar el archivo en el servidor.'
                ]);
            }

            for ($i = 0; $i < $totalChunks; $i++) {
                $partPath = $tempDir . '/chunk_' . $i;
                if (!file_exists($partPath)) {
                    fclose($out);
                    @unlink($finalPath);
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false, 
                        'error' => 'Falta el fragmento número ' . $i . ' para completar el ensamblado.'
                    ]);
                }

                $in = fopen($partPath, 'rb');
                while (!feof($in)) {
                    $buffer = fread($in, 4096 * 64);
                    fwrite($out, $buffer);
                }
                fclose($in);
                @unlink($partPath);
            }
            fclose($out);
            @rmdir($tempDir);

            return $this->response->setJSON([
                'success' => true,
                'completed' => true,
                'temp_storage' => $finalStorageName
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'completed' => false,
            'chunk_index' => $chunkIndex
        ]);
    }

    // ---------------------------------------------------------------------
    // Procesar la subida del archivo o archivos ensamblados y guardar en BD
    // ---------------------------------------------------------------------
    public function store()
    {
        $tempStorage = $this->request->getPost('temp_storage');
        $isChunked = !empty($tempStorage);

        $validationRules = [
            'filename' => [
                'label' => 'nombre del archivo',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'El nombre del archivo es obligatorio.',
                    'max_length' => 'El nombre del archivo no puede superar los 255 caracteres.'
                ]
            ],
            'download_limit' => [
                'label' => 'límite de descargas',
                'rules' => 'permit_empty|numeric|greater_than[0]',
                'errors' => [
                    'numeric' => 'El límite de descargas debe ser un número.',
                    'greater_than' => 'El límite de descargas debe ser mayor a 0.'
                ]
            ],
            'expires_at' => [
                'label' => 'fecha de expiración',
                'rules' => 'permit_empty|valid_date[d/m/Y]',
                'errors' => [
                    'valid_date' => 'La fecha de expiración debe tener el formato válido (DD/MM/YYYY).'
                ]
            ],
            'custom_slug' => [
                'label' => 'enlace personalizado',
                'rules' => 'permit_empty|alpha_dash|max_length[255]|is_unique[file_shares.slug]',
                'errors' => [
                    'alpha_dash' => 'El enlace personalizado solo puede contener letras, números, guiones y guiones bajos.',
                    'max_length' => 'El enlace personalizado no puede superar los 255 caracteres.',
                    'is_unique' => 'Este enlace personalizado ya está en uso, por favor elige otro.'
                ]
            ]
        ];

        if (!$isChunked) {
            $validationRules['uploaded_file'] = [
                'label' => 'archivo',
                'rules' => 'uploaded[uploaded_file]|max_size[uploaded_file,102400]',
                'errors' => [
                    'uploaded' => 'Por favor, selecciona al menos un archivo para subir.',
                    'max_size' => 'El archivo supera el límite de subida directo permitido.'
                ]
            ];
        }

        if (!$this->validate($validationRules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $uploadPath = WRITEPATH . 'uploads/files';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $finalFileName = $this->request->getPost('filename');
        $extension = '';

        if ($isChunked) {
            // Verificar archivo temporal ensamblado
            $safeTempStorage = basename($tempStorage);
            $tempFilePath = WRITEPATH . 'uploads/chunks/' . $safeTempStorage;

            if (!file_exists($tempFilePath)) {
                $err = 'No se encontró el archivo temporal ensamblado. Por favor, vuelve a intentar la subida.';
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => $err]);
                }
                return redirect()->back()->withInput()->with('error', $err);
            }

            $fileSize = filesize($tempFilePath);
            $extension = pathinfo($this->request->getPost('original_filename') ?? $finalFileName, PATHINFO_EXTENSION);
            $storageName = bin2hex(random_bytes(16));
            $destinationPath = $uploadPath . '/' . $storageName;

            if (!rename($tempFilePath, $destinationPath)) {
                $err = 'No se pudo mover el archivo al almacenamiento final.';
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => $err]);
                }
                return redirect()->back()->withInput()->with('error', $err);
            }

            $mimeType = mime_content_type($destinationPath) ?: 'application/octet-stream';
        } else {
            $file = $this->request->getFile('uploaded_file');
            if (!$file->isValid()) {
                return redirect()->back()->withInput()->with('error', 'El archivo subido no es válido.');
            }

            $storageName = bin2hex(random_bytes(16));
            if (!$file->move($uploadPath, $storageName)) {
                return redirect()->back()->withInput()->with('error', 'Fallo al guardar el archivo en el servidor.');
            }

            $destinationPath = $uploadPath . '/' . $storageName;
            $fileSize = $file->getSize();
            $mimeType = mime_content_type($destinationPath) ?: ($file->getClientMimeType() ?: 'application/octet-stream');
            $extension = pathinfo($file->getClientName(), PATHINFO_EXTENSION);
        }

        // Obtener slug personalizado o generar uno aleatorio
        $customSlug = $this->request->getPost('custom_slug');
        if (!empty($customSlug)) {
            $slug = strtolower($customSlug);
        } else {
            $slug = $this->generateUniqueSlug();
        }

        // Asegurar que el nombre tenga la extensión adecuada si corresponde
        if (!empty($extension) && !preg_match("/\." . preg_quote($extension, '/') . "$/i", $finalFileName)) {
            $finalFileName .= '.' . $extension;
        }

        // Procesar contraseña
        $passwordHash = null;
        $passwordRaw = $this->request->getPost('password');
        if (!empty($passwordRaw)) {
            $passwordHash = password_hash($passwordRaw, PASSWORD_DEFAULT);
        }

        // Calcular expiración
        $expiresAt = null;
        $expiresAtInput = $this->request->getPost('expires_at');
        if (!empty($expiresAtInput)) {
            $dateObj = \DateTime::createFromFormat('d/m/Y', $expiresAtInput);
            if ($dateObj !== false) {
                $expiresAt = $dateObj->format('Y-m-d') . ' 23:59:59';
            }
        }

        // Límite de descargas
        $downloadLimit = $this->request->getPost('download_limit');
        $downloadLimit = !empty($downloadLimit) ? (int)$downloadLimit : null;

        // Visibilidad y autodestrucción
        $isPublic = $this->request->getPost('is_public') !== null ? (int)$this->request->getPost('is_public') : 1;
        $autoDestroy = $this->request->getPost('auto_destroy') ? 1 : 0;

        // Guardar registro en BD
        $this->fileShareModel->save([
            'slug'           => $slug,
            'user_id'        => auth()->id(),
            'filename'       => $finalFileName,
            'storage_name'   => $storageName,
            'file_size'      => $fileSize,
            'mime_type'      => $mimeType,
            'password'       => $passwordHash,
            'expires_at'     => $expiresAt,
            'download_limit' => $downloadLimit,
            'download_count' => 0,
            'is_public'      => $isPublic,
            'auto_destroy'   => $autoDestroy
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'slug' => $slug,
                'share_url' => base_url('s/' . $slug),
                'message' => 'Transferencia completada con éxito.'
            ]);
        }

        return redirect()->to(base_url('files'))->with('message', 'Archivo subido y enlace de compartición generado exitosamente.');
    }

    // ---------------------------------------------------------------------
    // Mostrar formulario de edición de opciones
    // ---------------------------------------------------------------------
    public function edit($id)
    {
        $userId = auth()->id();
        $share = $this->fileShareModel->where('id', $id)->where('user_id', $userId)->first();

        if (!$share) {
            return redirect()->to(base_url('files'))->with('error', 'No se encontró el archivo o no tienes permisos para editarlo.');
        }

        $data = [
            'title' => 'Editar Archivo Compartido',
            'share' => $share
        ];

        echo view('template/header', $data);
        echo view('files/edit', $data);
        echo view('template/footer');
    }

    // ---------------------------------------------------------------------
    // Procesar la actualización de opciones del archivo
    // ---------------------------------------------------------------------
    public function update($id)
    {
        $userId = auth()->id();
        $share = $this->fileShareModel->where('id', $id)->where('user_id', $userId)->first();

        if (!$share) {
            return redirect()->to(base_url('files'))->with('error', 'No se encontró el archivo o no tienes permisos para editarlo.');
        }

        $validationRules = [
            'filename' => [
                'label' => 'nombre del archivo',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'El nombre del archivo es obligatorio.',
                    'max_length' => 'El nombre del archivo no puede superar los 255 caracteres.'
                ]
            ],
            'download_limit' => [
                'label' => 'límite de descargas',
                'rules' => 'permit_empty|numeric|greater_than[0]',
                'errors' => [
                    'numeric' => 'El límite de descargas debe ser un número.',
                    'greater_than' => 'El límite de descargas debe ser mayor a 0.'
                ]
            ],
            'expires_at' => [
                'label' => 'fecha de expiración',
                'rules' => 'permit_empty|valid_date[d/m/Y]',
                'errors' => [
                    'valid_date' => 'La fecha de expiración debe tener el formato válido (DD/MM/YYYY).'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mantener el slug original (ya no se permite editar)
        $slug = $share->slug;

        // Procesar contraseña
        $passwordHash = $share->password;
        $passwordRaw = $this->request->getPost('password');
        if (!empty($passwordRaw)) {
            $passwordHash = password_hash($passwordRaw, PASSWORD_DEFAULT);
        }

        // Calcular expiración
        $expiresAt = null;
        $expiresAtInput = $this->request->getPost('expires_at');
        if (!empty($expiresAtInput)) {
            $dateObj = \DateTime::createFromFormat('d/m/Y', $expiresAtInput);
            if ($dateObj !== false) {
                $expiresAt = $dateObj->format('Y-m-d') . ' 23:59:59';
            }
        }

        // Obtener límite de descargas
        $downloadLimit = $this->request->getPost('download_limit');
        $downloadLimit = ($downloadLimit !== null && $downloadLimit !== '') ? (int)$downloadLimit : null;

        // Visibilidad pública o privada
        $isPublic = $this->request->getPost('is_public') !== null ? (int)$this->request->getPost('is_public') : (int)($share->is_public ?? 1);
        $autoDestroy = $this->request->getPost('auto_destroy') ? 1 : 0;

        // Borrar contraseña explícitamente
        $removePassword = $this->request->getPost('remove_password');
        if (!empty($removePassword)) {
            $passwordHash = null;
        }

        // Verificar si se subió un archivo físico nuevo para reemplazar
        $file = $this->request->getFile('uploaded_file');
        $fileName = $this->request->getPost('filename');
        $fileSize = $share->file_size;
        $storageName = $share->storage_name;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Eliminar el archivo antiguo del disco
            $oldPath = WRITEPATH . 'uploads/files/' . $share->storage_name;
            if (is_file($oldPath)) {
                unlink($oldPath);
            }

            // Mover el nuevo archivo
            $uploadPath = WRITEPATH . 'uploads/files';
            $newStorageName = bin2hex(random_bytes(16));
            
            if ($file->move($uploadPath, $newStorageName)) {
                $fileSize = $file->getSize();
                $storageName = $newStorageName;
            } else {
                return redirect()->back()->withInput()->with('error', 'Fallo al guardar el nuevo archivo en el servidor.');
            }
        }

        // Asegurar que el nombre tenga la extensión correcta
        if (empty($fileName)) {
            $fileName = ($file && $file->isValid()) ? $file->getClientName() : $share->filename;
        } else {
            $extension = ($file && $file->isValid()) ? pathinfo($file->getClientName(), PATHINFO_EXTENSION) : pathinfo($share->filename, PATHINFO_EXTENSION);
            if (!empty($extension) && !preg_match("/\.$extension$/i", $fileName)) {
                $fileName .= '.' . $extension;
            }
        }

        $this->fileShareModel->update($id, [
            'slug'           => $slug,
            'filename'       => $fileName,
            'storage_name'   => $storageName,
            'file_size'      => $fileSize,
            'password'       => $passwordHash,
            'expires_at'     => $expiresAt,
            'download_limit' => $downloadLimit,
            'is_public'      => $isPublic,
            'auto_destroy'   => $autoDestroy
        ]);

        return redirect()->to(base_url('files'))->with('message', 'Las opciones para compartir han sido actualizadas.');
    }

    // ---------------------------------------------------------------------
    // Eliminar el archivo del disco y del registro de BD
    // ---------------------------------------------------------------------
    public function delete($id)
    {
        $userId = auth()->id();
        $share = $this->fileShareModel->where('id', $id)->where('user_id', $userId)->first();

        if (!$share) {
            return redirect()->to(base_url('files'))->with('error', 'No se encontró el archivo o no tienes permisos para eliminarlo.');
        }

        // Eliminar archivo físico (Cumplimiento de Regla 3: Limpieza de Servidor)
        $filePath = WRITEPATH . 'uploads/files/' . $share->storage_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Eliminar registro BD
        $this->fileShareModel->delete($id);

        return redirect()->to(base_url('files'))->with('message', 'El archivo compartido ha sido eliminado permanentemente.');
    }

    // ---------------------------------------------------------------------
    // Enviar el enlace de descarga por correo
    // ---------------------------------------------------------------------
    public function sendEmail($id)
    {
        $userId = auth()->id();
        $share = $this->fileShareModel->where('id', $id)->where('user_id', $userId)->first();

        if (!$share) {
            return redirect()->to(base_url('files'))->with('error', 'Archivo no encontrado.');
        }

        $emailRules = [
            'recipient_email' => [
                'label' => 'correo del destinatario',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El correo del destinatario es obligatorio.',
                    'valid_email' => 'Por favor, introduce una dirección de correo válida.'
                ]
            ]
        ];

        if (!$this->validate($emailRules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $recipient = $this->request->getPost('recipient_email');
        $senderName = auth()->user()->username;
        $downloadUrl = base_url('s/' . $share->slug);

        $emailService = \Config\Services::email();
        $settings = service('settings');

        // Configurar emisor
        $fromEmail = $settings->get('Email.fromEmail') ?: 'no-reply@filecrew.es';
        $fromName  = $settings->get('Email.fromName') ?: 'FileCrew';

        $emailService->setFrom($fromEmail, $fromName);
        $emailService->setTo($recipient);
        $emailService->setSubject("{$senderName} te ha enviado un archivo seguro");

        // Plantilla HTML Premium del correo
        $logoUrl = base_url('assets/images/logos/dark-logo.svg?v=' . filemtime(FCPATH . 'assets/images/logos/dark-logo.svg'));
        $messageBody = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        :root { color-scheme: light; }
    </style>
</head>
<body style="background-color: #ffffff; margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; background-image: linear-gradient(#ffffff, #ffffff); margin: 0; padding: 40px 20px; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
        <tr>
            <td align="center">
                <img src="' . $logoUrl . '" alt="Logo" style="max-width: 180px; margin-bottom: 30px; display: block;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 500px; background-color: #f8f9fa; background-image: linear-gradient(#f8f9fa, #f8f9fa); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e9ecef;">
                    <tr>
                        <td align="left" style="padding: 40px;">
                            <h2 style="color: #333f52; -webkit-text-fill-color: #333f52; margin-top: 0; text-align: center; font-weight: 600;">¡Te han enviado un archivo!</h2>
                            <p style="color: #5a6a85; -webkit-text-fill-color: #5a6a85; font-size: 16px; line-height: 1.6; text-align: center; margin-bottom: 25px;">
                                <strong>' . esc($senderName) . '</strong> ha compartido un archivo contigo de forma segura.
                            </p>
                            <div style="background-color: #ffffff; border-radius: 8px; border: 1px solid #e9ecef; padding: 20px; margin: 25px 0; text-align: center;">
                                <p style="margin: 0 0 10px 0; font-size: 16px; font-weight: bold; color: #2A3547; -webkit-text-fill-color: #2A3547;">' . esc($share->filename) . '</p>
                                <p style="margin: 0; font-size: 13px; color: #8c98a4; -webkit-text-fill-color: #8c98a4;">Tamaño: ' . $this->formatBytes($share->file_size) . '</p>
                            </div>
                            <div style="text-align: center; margin-bottom: 10px;">
                                <a href="' . $downloadUrl . '" style="display: inline-block; padding: 12px 24px; background-color: #F38020; background-image: linear-gradient(#F38020, #F38020); color: #ffffff; -webkit-text-fill-color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px;">
                                    Ver y Descargar Archivo
                                </a>
                            </div>
                        </td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 500px; margin-top: 20px;">
                    <tr>
                        <td align="center" style="padding: 0 20px;">
                            <p style="color: #8c98a4; -webkit-text-fill-color: #8c98a4; font-size: 11px; line-height: 1.5; margin: 0;">
                                &copy; ' . date('Y') . ' FileCrew
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        $emailService->setMessage($messageBody);

        if ($emailService->send()) {
            return redirect()->back()->with('message', "El enlace de descarga ha sido enviado exitosamente a {$recipient}.");
        } else {
            log_message('error', 'Fallo al enviar correo con enlace de descarga: ' . $emailService->printDebugger(['headers']));
            return redirect()->back()->with('error', 'Fallo al enviar el correo. Por favor, revisa tus Ajustes SMTP.');
        }
    }

    // ---------------------------------------------------------------------
    // Landing de descarga pública del archivo compartido
    // ---------------------------------------------------------------------
    public function showShare($slug)
    {
        $share = $this->fileShareModel->where('slug', $slug)->first();

        if (!$share) {
            return $this->showPublicError('Enlace no válido', 'El archivo solicitado no existe o el enlace es incorrecto.');
        }

        // Verificar caducidad por fecha
        if (!empty($share->expires_at) && Time::now()->isAfter(Time::parse($share->expires_at))) {
            if ($share->auto_destroy) {
                $this->destroyFileShare($share);
                return $this->showPublicError('Archivo Autodestruido', 'Este enlace de compartición ha caducado y el archivo se ha eliminado permanentemente de nuestros servidores.');
            }
            return $this->showPublicError('Enlace Expirado', 'Este enlace de compartición ha caducado por límite de tiempo.');
        }

        // Verificar caducidad por límite de descargas
        if (!empty($share->download_limit) && $share->download_count >= $share->download_limit) {
            if ($share->auto_destroy) {
                $this->destroyFileShare($share);
                return $this->showPublicError('Archivo Autodestruido', 'Este archivo ha alcanzado su límite de descargas y se ha eliminado permanentemente de nuestros servidores.');
            }
            return $this->showPublicError('Límite de Descargas Superado', 'Este archivo ya no está disponible porque alcanzó su límite máximo de descargas.');
        }


        // Si tiene contraseña y no se ha desbloqueado aún en la sesión actual
        $session = session();
        $unlockedShares = $session->get('unlocked_shares') ?: [];
        $requiresPassword = !empty($share->password) && !in_array($share->id, $unlockedShares);

        $fileHash = null;
        if (!$requiresPassword) {
            $filePath = WRITEPATH . 'uploads/files/' . $share->storage_name;
            if (file_exists($filePath)) {
                $fileHash = hash_file('sha256', $filePath);
            }
        }

        $data = [
            'title'            => 'Descargar Archivo',
            'share'            => $share,
            'requiresPassword' => $requiresPassword,
            'fileSizeFormatted'=> $this->formatBytes($share->file_size),
            'fileHash'         => $fileHash
        ];

        // Usamos una cabecera y pie de página limpios para los usuarios públicos
        echo view('template/public_header', $data);
        echo view('files/download', $data);
        echo view('template/public_footer');
    }

    // ---------------------------------------------------------------------
    // Verificar contraseña de un archivo protegido
    // ---------------------------------------------------------------------
    public function verifyPassword($slug)
    {
        $share = $this->fileShareModel->where('slug', $slug)->first();

        if (!$share) {
            return redirect()->to(base_url('/'))->with('error', 'Archivo no encontrado.');
        }

        // Límite de intentos por IP y enlace para frenar fuerza bruta (5 por minuto)
        $throttler = service('throttler');
        $throttleKey = 'pwd_verify_file_' . md5($this->request->getIPAddress() . '_' . $slug);
        if ($throttler->check($throttleKey, 5, MINUTE) === false) {
            $seconds = max(1, (int) $throttler->getTokenTime());
            return redirect()->to(base_url('s/' . $slug))->with('error', "Demasiados intentos. Espera {$seconds} segundos antes de volver a intentarlo.");
        }

        $passwordInput = $this->request->getPost('password');

        if (empty($passwordInput) || !password_verify($passwordInput, $share->password)) {
            return redirect()->to(base_url('s/' . $slug))->with('error', 'Contraseña incorrecta. Inténtalo de nuevo.');
        }

        // Desbloquear en la sesión del visitante
        $session = session();
        $unlockedShares = $session->get('unlocked_shares') ?: [];
        if (!in_array($share->id, $unlockedShares)) {
            $unlockedShares[] = $share->id;
            $session->set('unlocked_shares', $unlockedShares);
        }

        return redirect()->to(base_url('s/' . $slug));
    }

    // ---------------------------------------------------------------------
    // Ejecutar descarga física del archivo
    // ---------------------------------------------------------------------
    public function download($slug)
    {
        $share = $this->fileShareModel->where('slug', $slug)->first();

        if (!$share) {
            return redirect()->to(base_url('/'))->with('error', 'Archivo no disponible.');
        }

        // Volver a verificar expiraciones por seguridad
        if (!empty($share->expires_at) && Time::now()->isAfter(Time::parse($share->expires_at))) {
            if ($share->auto_destroy) {
                $this->destroyFileShare($share);
                return redirect()->to(base_url('/'))->with('error', 'El enlace ha caducado y el archivo se ha autodestruido.');
            }
            return redirect()->to(base_url('/'))->with('error', 'El enlace ha caducado.');
        }

        if (!empty($share->download_limit) && $share->download_count >= $share->download_limit) {
            if ($share->auto_destroy) {
                $this->destroyFileShare($share);
                return redirect()->to(base_url('/'))->with('error', 'Límite de descargas alcanzado y el archivo se ha autodestruido.');
            }
            return redirect()->to(base_url('/'))->with('error', 'Límite de descargas alcanzado.');
        }


        // Validar desbloqueo de contraseña
        if (!empty($share->password)) {
            $session = session();
            $unlockedShares = $session->get('unlocked_shares') ?: [];
            if (!in_array($share->id, $unlockedShares)) {
                return redirect()->to(base_url('s/' . $slug))->with('error', 'Se requiere contraseña para descargar.');
            }
        }

        $filePath = WRITEPATH . 'uploads/files/' . $share->storage_name;

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo no existe físicamente en el servidor.');
        }

        // Incrementar el contador de descargas
        $this->fileShareModel->update($share->id, [
            'download_count' => $share->download_count + 1
        ]);

        // Retornar archivo como stream de descarga
        return $this->response->download($filePath, null)->setFileName($this->safeDownloadName($share->filename));
    }

    // ---------------------------------------------------------------------
    // Descarga directa para el propietario del archivo (sin restricciones de clave/límite)
    // ---------------------------------------------------------------------
    public function ownerDownload($id)
    {
        $share = $this->fileShareModel->find($id);

        if (!$share) {
            return redirect()->back()->with('error', 'Archivo no encontrado.');
        }

        // Verificar que pertenezca al usuario autenticado, o sea administrador
        $user = auth()->user();
        if ($share->user_id !== $user->id && !$user->inGroup('superadmin', 'supervisor')) {
            return redirect()->back()->with('error', 'No tienes permiso para descargar este archivo.');
        }

        $filePath = WRITEPATH . 'uploads/files/' . $share->storage_name;

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo no existe físicamente en el servidor.');
        }

        // Retornar archivo como stream de descarga
        return $this->response->download($filePath, null)->setFileName($this->safeDownloadName($share->filename));
    }

    // ---------------------------------------------------------------------
    // Función auxiliar para eliminar el archivo físicamente y en BD
    // ---------------------------------------------------------------------
    private function destroyFileShare($share)
    {
        $filePath = WRITEPATH . 'uploads/files/' . $share->storage_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $this->fileShareModel->delete($share->id);
    }

    // ---------------------------------------------------------------------
    // Limpieza silenciosa de archivos autodestruibles caducados
    // Se ejecuta de fondo cuando se carga el panel
    // ---------------------------------------------------------------------
    private function cleanupExpiredFiles()
    {
        $shares = $this->fileShareModel->where('auto_destroy', 1)->findAll();
        
        foreach ($shares as $share) {
            $shouldDelete = false;

            // Verificar caducidad por fecha
            if (!empty($share->expires_at) && Time::now()->isAfter(Time::parse($share->expires_at))) {
                $shouldDelete = true;
            }

            // Verificar caducidad por límite de descargas
            if (!empty($share->download_limit) && $share->download_count >= $share->download_limit) {
                $shouldDelete = true;
            }

            if ($shouldDelete) {
                $this->destroyFileShare($share);
            }
        }
    }

    // ---------------------------------------------------------------------
    // Generar un slug único aleatorio de 12 caracteres
    // ---------------------------------------------------------------------
    private function generateUniqueSlug(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        
        do {
            $slug = '';
            for ($i = 0; $i < 12; $i++) {
                $slug .= $characters[random_int(0, $charactersLength - 1)];
            }
            // Verificar unicidad en base de datos
            $exists = $this->fileShareModel->where('slug', $slug)->countAllResults() > 0;
        } while ($exists);

        return $slug;
    }

    // ---------------------------------------------------------------------
    // Sanear el nombre de archivo para la cabecera Content-Disposition
    // ---------------------------------------------------------------------
    private function safeDownloadName(string $name): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F"\\\\\/]/u', '', $name);
        $name = trim((string) $name);

        return $name !== '' ? $name : 'archivo';
    }

    // ---------------------------------------------------------------------
    // Helper para formatear bytes de tamaño de archivo de forma amigable
    // ---------------------------------------------------------------------
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // ---------------------------------------------------------------------
    // Mostrar un error público amigable para enlaces caducados/inválidos
    // ---------------------------------------------------------------------
    private function showPublicError($title, $message)
    {
        $data = [
            'title'   => $title,
            'message' => $message
        ];

        echo view('template/public_header', $data);
        echo view('errors/public_share', $data);
        echo view('template/public_footer');
    }
}
