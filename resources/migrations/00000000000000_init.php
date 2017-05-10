<?php

use Phinx\Migration\AbstractMigration;

/**
 * Init.
 */
class Init extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // http://docs.phinx.org/en/latest/migrations.html#creating-a-table

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
                ->addColumn('created', 'datetime', ['null' => true, 'comment' => ''])
                ->addColumn('created_user_id', 'integer', ['null' => true, 'comment' => ''])
                ->addColumn('updated', 'datetime', ['null' => true, 'comment' => ''])
                ->addColumn('updated_user_id', 'integer', ['null' => true, 'comment' => ''])
                ->addColumn('deleted', 'datetime', ['null' => true, 'comment' => ''])
                ->addColumn('deleted_user_id', 'integer', ['null' => true, 'comment' => ''])
                ->addIndex(['username'], ['unique' => true])
                ->addIndex(['created_user_id'])
                ->addIndex(['updated_user_id'])
                ->addIndex(['deleted_user_id'])
                //->addForeignKey('created_user_id', 'users', 'id')
                //->addForeignKey('updated_user_id', 'users', 'id')
                //->addForeignKey('deleted_user_id', 'users', 'id')
                ->save();

        // Insert records
        $this->execute("insert into `users` (`id`, `username`, `password`, `disabled`, `role`, `locale`)
            values('1','admin','$2y$10$8SCHkI4JUKJ2NA353BTHW.Kgi33HI.2C35xd/j5YUzBx05F1O4lJO','0','ROLE_ADMIN','en_US');");

        $this->execute("insert into `users` (`id`, `username`, `password`, `disabled`, `role`, `locale`)
        values('2', 'user', '$1\$X64.UA0.\$kCSxRsj3GKk7Bwy3P6xn1.', '0', 'ROLE_USER', 'de_DE');");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
    }
}
