<?php namespace Tatter\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_visits extends Migration
{
	public function up()
	{
		$fields = [
			'session_id'   => ['type' => 'VARCHAR', 'constraint' => 32],
			'user_id'      => ['type' => 'INT', 'null' => true],
			'ip_address'   => ['type' => 'BIGINT', 'null' => true],
			'user_agent'   => ['type' => 'VARCHAR', 'constraint' => 255],
			'scheme'       => ['type' => 'VARCHAR', 'constraint' => 15],
			'host'         => ['type' => 'VARCHAR', 'constraint' => 63],
			'port'         => ['type' => 'VARCHAR', 'constraint' => 15],
			'user'         => ['type' => 'VARCHAR', 'constraint' => 31],
			'pass'         => ['type' => 'VARCHAR', 'constraint' => 255],
			'path'         => ['type' => 'VARCHAR', 'constraint' => 255],
			'query'        => ['type' => 'VARCHAR', 'constraint' => 255],
			'fragment'     => ['type' => 'VARCHAR', 'constraint' => 31],
			'views'        => ['type' => 'INT', 'default' => 1],
			'created_at'   => ['type' => 'DATETIME', 'null' => true],
			'updated_at'   => ['type' => 'DATETIME', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('session_id');
		$this->forge->addKey('user_id');
		$this->forge->addKey('ip_address');
		$this->forge->addKey(['host', 'path', 'query']);
		$this->forge->addKey('created_at');
		$this->forge->addKey('updated_at');
		
		$this->forge->createTable('visits');
	}

	public function down()
	{
		$this->forge->dropTable('visits');
	}
}
