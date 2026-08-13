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

        // Consultar los últimos logs de envío con límite dinámico
        $limit = $this->request->getGet('limit') ?: 10;
        $limit = max(5, min(100, (int)$limit));

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
        
        $data['recentShares'] = $recentShares;
        $data['limit']        = $limit;

        echo view('template/header', $data);
        echo view('dashboards/index', $data);
        echo view('template/footer');
    }
}
