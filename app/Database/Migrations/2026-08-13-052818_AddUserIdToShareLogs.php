<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdToShareLogs extends Migration
{
    public function up()
    {
        // Dropping and recreating table to support SQLite alterations cleanly
        $this->forge->dropTable('share_logs', true);

        $this->forge->addField([
            'id'              => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id'         => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'recipient_email' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'resource_type'   => [
                'type'           => 'VARCHAR',
                'constraint'     => 50, // 'file' or 'password'
            ],
            'resource_name'   => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'status'          => [
                'type'           => 'VARCHAR',
                'constraint'     => 50, // 'success' or 'failed'
            ],
            'created_at'      => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('share_logs');
    }

    public function down()
    {
        $this->forge->dropTable('share_logs');
    }
}
