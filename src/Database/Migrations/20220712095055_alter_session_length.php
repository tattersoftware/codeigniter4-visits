<?php

namespace Tatter\Visits\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterSessionLength extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        $this->forge->modifyColumn('visits', [
            'session_id' => ['type' => 'varchar', 'constraint' => 127],
        ]);
    }

    /**
     * @return void
     */
    public function down()
    {
        $this->forge->modifyColumn('visits', [
            'session_id' => ['type' => 'varchar', 'constraint' => 32],
        ]);
    }
}
