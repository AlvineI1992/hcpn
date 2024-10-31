<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJwtTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'token' => [
                'type' => 'TEXT',
                'null' => false,
                'comment' => 'JWT token string'
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'comment' => 'Expiration time of the token'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Time token was issued'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'on_update' => 'CURRENT_TIMESTAMP',
                'comment' => 'Last time the token was updated'
            ]
        ]);

        // Set primary key
        $this->forge->addKey('id', true);
        // Add index for user_id for quick lookup
        $this->forge->addKey('user_id');

        // Create the table
        $this->forge->createTable('jwt_tokens');
    }

    public function down()
    {
        // Drop the table if it exists
        $this->forge->dropTable('jwt_tokens');
    }
}