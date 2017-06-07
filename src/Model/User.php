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
     * @return array|false A row
     */
    public function findById($id)
    {
        $query = $this->db->newQuery()
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
        $query = $this->db->newQuery()
            ->select(['*'])
            ->from('test');
        $rows = $query->execute()->fetchAll('assoc');
        return $rows;
    }
}
