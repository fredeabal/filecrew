<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasswordSharesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => '12',
            ],
            'user_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'encrypted_content' => [
                'type'       => 'TEXT',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'view_limit' => [
                'type'       => 'INTEGER',
                'null'       => true,
            ],
            'view_count' => [
                'type'       => 'INTEGER',
                'default'    => 0,
            ],
            'is_public' => [
                'type'       => 'TINYINT',
                'default'    => 1,
            ],
            'auto_destroy' => [
                'type'       => 'TINYINT',
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('password_shares', true);

        // Asignar el nuevo permiso al rol superadmin si la tabla existe
        $db = \Config\Database::connect();
        if ($db->tableExists('auth_group_permissions')) {
            $exists = $db->table('auth_group_permissions')
                         ->where('group', 'superadmin')
                         ->where('permission', 'admin.passwords')
                         ->countAllResults();

            if ($exists === 0) {
                $db->table('auth_group_permissions')->insert([
                    'group'      => 'superadmin',
                    'permission' => 'admin.passwords',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('password_shares', true);

        // Eliminar el permiso asignado
        $db = \Config\Database::connect();
        if ($db->tableExists('auth_group_permissions')) {
            $db->table('auth_group_permissions')
               ->where('group', 'superadmin')
               ->where('permission', 'admin.passwords')
               ->delete();
        }
    }
}
