<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $fileShareModel = new \App\Models\FileShareModel();
        
        $data = [
            'title' => 'Dashboard'
        ];

        // Todos ven el mismo dashboard por ahora (sus propios archivos y contraseñas)
        $userId = $user->id;
        $data['filesCount'] = $fileShareModel->where('user_id', $userId)->countAllResults();
        
        $passwordShareModel = new \App\Models\PasswordShareModel();
        $data['passwordsCount'] = $passwordShareModel->where('user_id', $userId)->countAllResults();
        
        $db = \Config\Database::connect();
        $userSumQuery = $db->table('file_shares')
                          ->selectSum('download_count')
                          ->selectSum('file_size')
                          ->where('user_id', $userId)
                          ->get()
                          ->getRow();
        
        $data['downloadsCount'] = (int)($userSumQuery->download_count ?? 0);
        
        // Formatear espacio usado
        $bytes = (float)($userSumQuery->file_size ?? 0);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes = $bytes > 0 ? $bytes / pow(1024, $pow) : 0;
        $data['spaceUsed'] = round($bytes, 2) . ' ' . $units[$pow];

        // Consultar los últimos 25 logs de envío realizados
        $limit = 25;

        $recentShares = [];
        try {
            $recentShares = $db->table('share_logs')
                               ->select('share_logs.*, COALESCE(users.username, \'Invitado\') as sender_username')
                               ->join('users', 'users.id = share_logs.user_id', 'left')
                               ->where('share_logs.user_id', auth()->id())
                               ->orderBy('share_logs.created_at', 'DESC')
                               ->limit($limit)
                               ->get()
                               ->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error al consultar share_logs en Dashboard: ' . $e->getMessage());
        }

        // Vincular recurso original para permitir acciones directas (editar, descargar, copiar enlace)
        foreach ($recentShares as $log) {
            $log->resource_id   = null;
            $log->resource_slug = null;
            if ($log->resource_type === 'file') {
                $file = $db->table('file_shares')
                           ->select('id, slug')
                           ->where('user_id', $log->user_id)
                           ->where('filename', $log->resource_name)
                           ->orderBy('id', 'DESC')
                           ->get(1)
                           ->getRow();
                if ($file) {
                    $log->resource_id   = $file->id;
                    $log->resource_slug = $file->slug;
                }
            } elseif ($log->resource_type === 'password') {
                $pwd = $db->table('password_shares')
                          ->select('id, slug')
                          ->where('user_id', $log->user_id)
                          ->where('title', $log->resource_name)
                          ->orderBy('id', 'DESC')
                          ->get(1)
                          ->getRow();
                if ($pwd) {
                    $log->resource_id   = $pwd->id;
                    $log->resource_slug = $pwd->slug;
                }
            }
        }
        
        $data['recentShares']     = $recentShares;
        $data['limit']            = $limit;
        $data['totalRecentShares'] = $db->table('share_logs')->where('user_id', auth()->id())->countAllResults();

        echo view('template/header', $data);
        echo view('dashboards/index', $data);
        echo view('template/footer');
    }
}
