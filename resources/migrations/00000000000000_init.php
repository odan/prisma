<?php

use Phinx\Migration\AbstractMigration;

/**
 * Init.
 */
class Init extends AbstractMigration
{
    /**
     * Change method.
     */
    public function change()
    {
        $table = $this->table('users', [
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci',
            'comment' => '',
            //'id' => false,
            //'primary_key' => 'id',
        ]);

        // Phinx automatically creates an auto-incrementing primary key
        // column called id for every table. To specify an alternate primary
        // key you can specify the primary_key option
        //$table->addColumn('id', 'biginteger', array('identity' => true, 'null' => false, 'comment' => ''));
        //
        // Index name: field
        // Foreign key name must be unique: <tablename>_ibfk_<counter>
        //
        // Add columns
        $table->addColumn('username', 'string', ['limit' => 255, 'null' => true, 'comment' => 'aaa'])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => true, 'comment' => ''])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => true, 'comment' => ''])
            ->addColumn('first_name', 'string', ['limit' => 255, 'null' => true, 'comment' => ''])
            ->addColumn('last_name', 'string', ['limit' => 255, 'null' => true, 'comment' => ''])
            ->addColumn('role', 'string', ['limit' => 255, 'null' => true, 'comment' => ''])
            ->addColumn('locale', 'string', ['limit' => 255, 'null' => true, 'comment' => ''])
            ->addColumn('disabled', 'boolean', ['null' => false, 'default' => 0, 'comment' => ''])
            ->addColumn('created_at', 'datetime', ['null' => true, 'comment' => ''])
            ->addColumn('created_by', 'integer', ['null' => true, 'comment' => ''])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'comment' => ''])
            ->addColumn('updated_by', 'integer', ['null' => true, 'comment' => ''])
            ->addIndex(['username'], ['unique' => true])
            ->addIndex(['created_by'])
            ->addIndex(['updated_by'])
            ->save();
    }

}
