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

        // Últimos 10 archivos compartidos reales del usuario
        $data['recentFiles'] = $fileShareModel->where('user_id', $userId)
                                              ->orderBy('created_at', 'DESC')
                                              ->limit(10)
                                              ->findAll();

        echo view('template/header', $data);
        echo view('dashboards/index', $data);
        echo view('template/footer');
    }
}
