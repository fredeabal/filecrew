<?php

// ---------------------------------------------------------------------
// Helper para registro de envíos (share_logs)
// ---------------------------------------------------------------------

if (! function_exists('log_share')) {
    /**
     * Registra un evento de envío de archivo o contraseña en la BD.
     *
     * @param string $recipient    Email del destinatario
     * @param string $resourceType Tipo de recurso ('file' o 'password')
     * @param string $resourceName Nombre del recurso compartido
     * @param string $status       Estado ('success' o 'failed')
     * @return bool
     */
    function log_share(string $recipient, string $resourceType, string $resourceName, string $status): bool
    {
        try {
            $db = \Config\Database::connect();
            $user = auth()->user();
            $userId = $user ? $user->id : null;

            return $db->table('share_logs')->insert([
                'user_id'         => $userId,
                'recipient_email' => $recipient,
                'resource_type'   => $resourceType,
                'resource_name'   => $resourceName,
                'status'          => $status,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al registrar share_log: ' . $e->getMessage());
            return false;
        }
    }
}
