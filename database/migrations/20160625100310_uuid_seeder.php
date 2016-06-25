<?php

use Phinx\Migration\AbstractMigration;
use Ramsey\Uuid\Uuid;

class UuidSeeder extends AbstractMigration
{
    protected $table = 'user_sessions';
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $rows = $this->fetchAll('SELECT id FROM ' . $this->table . ' WHERE uuid=\'\'');
        set_time_limit(60 * 15);
        foreach ($rows as $row) {
            $this->execute('UPDATE ' . $this->table . ' SET uuid=\'' . Uuid::uuid4()->toString() . '\' WHERE id=' . $row['id']);
        }

        $table = $this->table($this->table);
        $table
            ->addIndex('uuid', ['unique' => true])
            ->update();
    }
}
