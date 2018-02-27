<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class Init extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER DATABASE CHARACTER SET 'utf8';");
        $this->execute("ALTER DATABASE COLLATE='utf8_unicode_ci';");
        $table = $this->table("users", ['engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_unicode_ci", 'comment' => ""]);
        $table->save();
        if ($this->table('users')->hasColumn('id')) {
            $this->table("users")->changeColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->update();
        } else {
            $this->table("users")->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->update();
        }
        $table->addColumn('username', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'comment' => "aaa", 'after' => 'id'])->update();
        $table->addColumn('password', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'username'])->update();
        $table->addColumn('email', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'password'])->update();
        $table->addColumn('first_name', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'email'])->update();
        $table->addColumn('last_name', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'first_name'])->update();
        $table->addColumn('role', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'last_name'])->update();
        $table->addColumn('locale', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'role'])->update();
        $table->addColumn('disabled', 'boolean', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'locale'])->update();
        $table->addColumn('created_at', 'datetime', ['null' => true, 'after' => 'disabled'])->update();
        $table->addColumn('created_by', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'created_at'])->update();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_by'])->update();
        $table->addColumn('updated_by', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])->update();
        $table->save();
        if($this->table('users')->hasIndex('username')) {
            $this->table("users")->removeIndexByName('username');
        }
        $this->table("users")->addIndex(['username'], ['name' => "username", 'unique' => true])->save();
        if($this->table('users')->hasIndex('created_by')) {
            $this->table("users")->removeIndexByName('created_by');
        }
        $this->table("users")->addIndex(['created_by'], ['name' => "created_by", 'unique' => false])->save();
        if($this->table('users')->hasIndex('updated_by')) {
            $this->table("users")->removeIndexByName('updated_by');
        }
        $this->table("users")->addIndex(['updated_by'], ['name' => "updated_by", 'unique' => false])->save();
    }
}
