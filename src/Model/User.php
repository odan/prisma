<?php

namespace App\Model;

/**
 * User
 */
class User extends BaseModel
{

    /**
     * Get user by id
     *
     * @param int $id User id
     * @return array Rows
     */
    public function getById($id)
    {
        $db = $this->getDb();
        $query = $db->newQuery()
                ->select(['id', 'firstname', 'lastname', 'deleted', 'updated_at'])
                ->from('test')
                ->where(['id' => $id]);

        $row = $query->execute()->fetch('assoc');
        return $row;
    }

    /**
     * Get user by id
     *
     * @return array Rows
     */
    public function getAll()
    {
        $query = $this->getDb()->newQuery()
                ->select(['*'])
                ->from('test');
        $rows = $query->execute()->fetchAll('assoc');
        return $rows;
    }
}
