<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PasswordShareModel;
use CodeIgniter\I18n\Time;

class PasswordShareController extends BaseController
{
    protected $passwordShareModel;
    protected $perPage = 15;

    public function __construct()
    {
        $this->passwordShareModel = new PasswordShareModel();
    }

    // ---------------------------------------------------------------------
    // Listado de contraseñas compartidas por el usuario actual
    // ---------------------------------------------------------------------
    public function index()
    {
        // Limpiar contraseñas vencidos con autodestrucción
        $this->cleanupExpiredPasswords();

        $userId = auth()->id();
        $query = $this->passwordShareModel->where('user_id', $userId);

        $passwords = $query->orderBy('created_at', 'DESC')->paginate($this->perPage, 'passwords');
        $pager = $this->passwordShareModel->pager;

        $data = [
            'title' => 'Contraseñas',
            'passwords' => $passwords,
            'pager' => $pager
        ];

        echo view('template/header', $data);
        echo view('passwords/list', $data);
        echo view('template/footer');
    }

    // ---------------------------------------------------------------------
    // Mostrar formulario de creación de contraseñas
    // ---------------------------------------------------------------------
    public function create()
    {
        $data = [
            'title' => 'Compartir Contraseña'
        ];

        echo view('template/header', $data);
        echo view('passwords/create', $data);
        echo view('template/footer');
    }

    // ---------------------------------------------------------------------
    // Procesar la creación de la contraseña
    // ---------------------------------------------------------------------
    public function store()
    {
        $validationRules = [
            'password_content' => [
                'label' => 'contenido de la contraseña',
                'rules' => 'required',
                'errors' => [
                    'required' => 'El contenido de la contraseña es obligatorio.'
                ]
            ],
            'title' => [
                'label' => 'nombre de la contraseña',
                'rules' => 'permit_empty|max_length[255]',
                'errors' => [
                    'max_length' => 'El nombre no puede superar los 255 caracteres.'
                ]
            ],
            'view_limit' => [
                'label' => 'límite máximo de visualizaciones',
                'rules' => 'permit_empty|numeric|greater_than[0]',
                'errors' => [
                    'numeric' => 'El límite máximo de visualizaciones debe ser un número.',
                    'greater_than' => 'El límite máximo de visualizaciones debe ser mayor a 0.'
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
                'rules' => 'permit_empty|alpha_dash|max_length[255]|is_unique[password_shares.slug]',
                'errors' => [
                    'alpha_dash' => 'El enlace personalizado solo puede contener letras, números, guiones y guiones bajos.',
                    'max_length' => 'El enlace personalizado no puede superar los 255 caracteres.',
                    'is_unique' => 'Este enlace personalizado ya está en uso, por favor elige otro.'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener slug personalizado o generar uno aleatorio
        $customSlug = $this->request->getPost('custom_slug');
        if (!empty($customSlug)) {
            $slug = strtolower($customSlug);
        } else {
            $slug = $this->generateUniqueSlug();
        }

        // Procesar contraseña extra (opcional)
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

        // Obtener límite máximo de visualizaciones
        $viewLimit = $this->request->getPost('view_limit');
        $viewLimit = !empty($viewLimit) ? (int)$viewLimit : null;

        $autoDestroy = $this->request->getPost('auto_destroy') ? 1 : 0;
        $title = $this->request->getPost('title');
        
        if (empty($title)) {
            $title = 'Contraseña compartido';
        }

        // Encriptar contenido
        $passwordContent = $this->request->getPost('password_content');
        $encrypter = \Config\Services::encrypter();
        $encryptedContent = base64_encode($encrypter->encrypt($passwordContent));

        // Guardar metadata en BD
        $this->passwordShareModel->save([
            'slug'              => $slug,
            'user_id'           => auth()->id(),
            'title'             => $title,
            'encrypted_content' => $encryptedContent,
            'password'          => $passwordHash,
            'expires_at'        => $expiresAt,
            'view_limit'        => $viewLimit,
            'view_count'        => 0,
            'is_public'         => 1, // Siempre público (por URL)
            'auto_destroy'      => $autoDestroy
        ]);

        return redirect()->to(base_url('passwords'))->with('message', 'Contraseña protegido y enlace de compartición generado exitosamente.');
    }

    // ---------------------------------------------------------------------
    // Mostrar formulario de edición de opciones
    // ---------------------------------------------------------------------
    public function edit($id)
    {
        $userId = auth()->id();
        $password = $this->passwordShareModel->where('id', $id)->where('user_id', $userId)->first();

        if (!$password) {
            return redirect()->to(base_url('passwords'))->with('error', 'No se encontró la contraseña o no tienes permisos para editarlo.');
        }

        $data = [
            'title' => 'Editar Ajustes de la Contraseña',
            'password' => $password
        ];

        echo view('template/header', $data);
        echo view('passwords/edit', $data);
        echo view('template/footer');
    }

    // ---------------------------------------------------------------------
    // Procesar la actualización de opciones
    // ---------------------------------------------------------------------
    public function update($id)
    {
        $userId = auth()->id();
        $password = $this->passwordShareModel->where('id', $id)->where('user_id', $userId)->first();

        if (!$password) {
            return redirect()->to(base_url('passwords'))->with('error', 'No se encontró la contraseña o no tienes permisos para editarlo.');
        }

        $validationRules = [
            'title' => [
                'label' => 'nombre de la contraseña',
                'rules' => 'permit_empty|max_length[255]',
                'errors' => [
                    'max_length' => 'El nombre no puede superar los 255 caracteres.'
                ]
            ],
            'view_limit' => [
                'label' => 'límite máximo de visualizaciones',
                'rules' => 'permit_empty|numeric|greater_than[0]',
                'errors' => [
                    'numeric' => 'El límite máximo de visualizaciones debe ser un número.',
                    'greater_than' => 'El límite máximo de visualizaciones debe ser mayor a 0.'
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

        // Procesar contraseña
        $passwordHash = $password->password;
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

        // Obtener límite máximo de visualizaciones
        $viewLimit = $this->request->getPost('view_limit');
        $viewLimit = ($viewLimit !== null && $viewLimit !== '') ? (int)$viewLimit : null;

        $autoDestroy = $this->request->getPost('auto_destroy') ? 1 : 0;
        $title = $this->request->getPost('title');
        
        if (empty($title)) {
            $title = $password->title;
        }

        // Borrar contraseña explícitamente
        $removePassword = $this->request->getPost('remove_password');
        if (!empty($removePassword)) {
            $passwordHash = null;
        }

        $this->passwordShareModel->update($id, [
            'title'          => $title,
            'password'       => $passwordHash,
            'expires_at'     => $expiresAt,
            'view_limit'     => $viewLimit,
            'auto_destroy'   => $autoDestroy
        ]);

        return redirect()->to(base_url('passwords'))->with('message', 'Las opciones para compartir han sido actualizadas.');
    }

    // ---------------------------------------------------------------------
    // Eliminar registro
    // ---------------------------------------------------------------------
    public function delete($id)
    {
        $userId = auth()->id();
        $password = $this->passwordShareModel->where('id', $id)->where('user_id', $userId)->first();

        if (!$password) {
            return redirect()->to(base_url('passwords'))->with('error', 'No se encontró la contraseña o no tienes permisos para eliminarlo.');
        }

        $this->passwordShareModel->delete($id);

        return redirect()->to(base_url('passwords'))->with('message', 'La contraseña ha sido eliminado permanentemente.');
    }

    // ---------------------------------------------------------------------
    // Landing de vista pública de la contraseña
    // ---------------------------------------------------------------------
    public function showShare($slug)
    {
        $password = $this->passwordShareModel->where('slug', $slug)->first();

        if (!$password) {
            return $this->showPublicError('Enlace no válido', 'La contraseña solicitado no existe o el enlace es incorrecto.');
        }

        // Verificar caducidad
        if (!empty($password->expires_at) && Time::now()->isAfter(Time::parse($password->expires_at))) {
            if ($password->auto_destroy) {
                $this->passwordShareModel->delete($password->id);
                return $this->showPublicError('Contraseña Autodestruido', 'Este enlace ha caducado y la contraseña se ha eliminado permanentemente de nuestros servidores.');
            }
            return $this->showPublicError('Enlace Expirado', 'Este enlace ha caducado por límite de tiempo.');
        }

        if (!empty($password->view_limit) && $password->view_count >= $password->view_limit) {
            if ($password->auto_destroy) {
                $this->passwordShareModel->delete($password->id);
                return $this->showPublicError('Contraseña Autodestruido', 'Esta contraseña ha alcanzado su límite de visualizaciones y se ha eliminado permanentemente.');
            }
            return $this->showPublicError('Límite Superado', 'Esta contraseña ya no está disponible porque alcanzó su límite máximo de visualizaciones.');
        }

        $session = session();
        $unlockedPasswords = $session->get('unlocked_passwords') ?: [];
        $requiresPassword = !empty($password->password) && !in_array($password->id, $unlockedPasswords);

        $data = [
            'title'            => 'Revelar Contraseña',
            'password'           => $password,
            'requiresPassword' => $requiresPassword
        ];

        echo view('template/public_header', $data);
        echo view('passwords/public_view', $data);
        echo view('template/public_footer');
    }

    // ---------------------------------------------------------------------
    // Verificar contraseña de una contraseña protegido
    // ---------------------------------------------------------------------
    public function verifyPassword($slug)
    {
        $password = $this->passwordShareModel->where('slug', $slug)->first();

        if (!$password) {
            return redirect()->back()->with('error', 'Contraseña no encontrado.');
        }

        $passwordInput = $this->request->getPost('password');

        if (empty($passwordInput) || !password_verify($passwordInput, $password->password)) {
            return redirect()->back()->with('error', 'Contraseña incorrecta. Inténtalo de nuevo.');
        }

        // Desbloquear en la sesión del visitante
        $session = session();
        $unlockedPasswords = $session->get('unlocked_passwords') ?: [];
        if (!in_array($password->id, $unlockedPasswords)) {
            $unlockedPasswords[] = $password->id;
            $session->set('unlocked_passwords', $unlockedPasswords);
        }

        return redirect()->to(base_url('pwd/' . $slug));
    }

    // ---------------------------------------------------------------------
    // Revelar la contraseña (descifra y muestra)
    // ---------------------------------------------------------------------
    public function revealShare($slug)
    {
        $password = $this->passwordShareModel->where('slug', $slug)->first();

        if (!$password) {
            return redirect()->to(base_url('/'))->with('error', 'Contraseña no disponible.');
        }

        // Volver a verificar expiraciones
        if (!empty($password->expires_at) && Time::now()->isAfter(Time::parse($password->expires_at))) {
            if ($password->auto_destroy) {
                $this->passwordShareModel->delete($password->id);
                return redirect()->to(base_url('/'))->with('error', 'El enlace ha caducado y la contraseña se ha autodestruido.');
            }
            return redirect()->to(base_url('/'))->with('error', 'El enlace ha caducado.');
        }

        if (!empty($password->view_limit) && $password->view_count >= $password->view_limit) {
            if ($password->auto_destroy) {
                $this->passwordShareModel->delete($password->id);
                return redirect()->to(base_url('/'))->with('error', 'Límite de visualizaciones alcanzado y la contraseña se ha autodestruido.');
            }
            return redirect()->to(base_url('/'))->with('error', 'Límite de visualizaciones alcanzado.');
        }

        // Validar desbloqueo
        if (!empty($password->password)) {
            $session = session();
            $unlockedPasswords = $session->get('unlocked_passwords') ?: [];
            if (!in_array($password->id, $unlockedPasswords)) {
                return redirect()->to(base_url('pwd/' . $slug))->with('error', 'Se requiere contraseña.');
            }
        }

        // Descifrar contenido
        $encrypter = \Config\Services::encrypter();
        try {
            $decryptedContent = $encrypter->decrypt(base64_decode($password->encrypted_content));
        } catch (\Exception $e) {
            log_message('error', 'Fallo al descifrar contraseña: ' . $e->getMessage());
            return $this->showPublicError('Error de Cifrado', 'No se pudo descifrar el contenido. Posible corrupción de datos.');
        }

        // Incrementar el contador de vistas
        $this->passwordShareModel->update($password->id, [
            'view_count' => $password->view_count + 1
        ]);

        // Si auto_destroy está activo y hemos alcanzado el límite ahora, lo borramos inmediatamente?
        // En PrivateBin u otros, si es de "1 vista", se borra instantáneamente de la BD después de descifrar, para que ni recargando la página vuelva a salir.
        $destroyedNow = false;
        if ($password->auto_destroy) {
            $newCount = $password->view_count + 1;
            if (!empty($password->view_limit) && $newCount >= $password->view_limit) {
                $this->passwordShareModel->delete($password->id);
                $destroyedNow = true;
            }
        }

        $data = [
            'title' => 'Tu Contraseña Compartida',
            'decryptedContent' => $decryptedContent,
            'destroyedNow' => $destroyedNow,
            'password' => $password
        ];

        echo view('template/public_header', $data);
        echo view('passwords/public_reveal', $data);
        echo view('template/public_footer');
    }

    // ---------------------------------------------------------------------
    // Enviar el enlace de la contraseña por correo
    // ---------------------------------------------------------------------
    public function sendEmail($id)
    {
        helper('share');
        $userId = auth()->id();
        $share = $this->passwordShareModel->where('id', $id)->where('user_id', $userId)->first();

        if (!$share) {
            return redirect()->to(base_url('passwords'))->with('error', 'Contraseña no encontrada.');
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
        $downloadUrl = base_url('pwd/' . $share->slug);

        $emailService = \Config\Services::email();
        $settings = service('settings');

        // Configurar emisor
        $fromEmail = $settings->get('Email.fromEmail') ?: 'no-reply@filecrew.es';
        $fromName  = $settings->get('Email.fromName') ?: 'FileCrew';

        $emailService->setFrom($fromEmail, $fromName);
        $emailService->setTo($recipient);
        $emailService->setSubject("{$senderName} te ha enviado una contraseña segura");

        // Plantilla HTML Premium del correo
        $logoUrl = base_url('assets/images/logos/dark-logo.svg?v=' . filemtime(FCPATH . 'assets/images/logos/dark-logo.svg'));
        $appUrl = base_url();

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
                            <h2 style="color: #333f52; -webkit-text-fill-color: #333f52; margin-top: 0; text-align: center; font-weight: 600;">¡Te han enviado una contraseña!</h2>
                            <p style="color: #5a6a85; -webkit-text-fill-color: #5a6a85; font-size: 16px; line-height: 1.6; text-align: center; margin-bottom: 25px;">
                                <strong>' . esc($senderName) . '</strong> ha compartido una contraseña contigo de forma segura.
                            </p>
                            <div style="background-color: #ffffff; border-radius: 8px; border: 1px solid #e9ecef; padding: 20px; margin: 25px 0; text-align: center;">
                                <p style="margin: 0; font-size: 16px; font-weight: bold; color: #2A3547; -webkit-text-fill-color: #2A3547;">' . esc($share->title) . '</p>
                            </div>
                            <div style="text-align: center; margin-bottom: 10px;">
                                <a href="' . $downloadUrl . '" style="display: inline-block; padding: 12px 24px; background-color: #F38020; background-image: linear-gradient(#F38020, #F38020); color: #ffffff; -webkit-text-fill-color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px;">
                                    Ver Contraseña
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
            log_share($recipient, 'password', $share->title, 'success');
            return redirect()->back()->with('message', "El enlace ha sido enviado exitosamente a {$recipient}.");
        } else {
            log_share($recipient, 'password', $share->title, 'failed');
            log_message('error', 'Error al enviar correo: ' . $emailService->printDebugger(['headers']));
            return redirect()->back()->with('error', 'Hubo un error al enviar el correo. Por favor, revisa la configuración del sistema.');
        }
    }

    // ---------------------------------------------------------------------
    // Limpieza silenciosa

    // ---------------------------------------------------------------------
    private function cleanupExpiredPasswords()
    {
        $passwords = $this->passwordShareModel->where('auto_destroy', 1)->findAll();
        
        foreach ($passwords as $password) {
            $shouldDelete = false;

            if (!empty($password->expires_at) && Time::now()->isAfter(Time::parse($password->expires_at))) {
                $shouldDelete = true;
            }
            if (!empty($password->view_limit) && $password->view_count >= $password->view_limit) {
                $shouldDelete = true;
            }

            if ($shouldDelete) {
                $this->passwordShareModel->delete($password->id);
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
                $slug .= $characters[rand(0, $charactersLength - 1)];
            }
            $exists = $this->passwordShareModel->where('slug', $slug)->countAllResults() > 0;
        } while ($exists);

        return $slug;
    }

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
