<?php

namespace App\Repository;

use App\Entity\UserEntity;

/**
 * User
 */
class UserRepository extends BaseRepository
{

    /**
     * Get user by id
     *
     * @param int $id User id
     * @return UserEntity|null A row
     */
    public function findById($id)
    {
        $query = $this->db->newQuery()
            ->select(['id', 'username', 'first_name', 'last_name', 'created', 'updated'])
            ->from('users')
            ->where(['id' => $id]);

        if (!$row = $query->execute()->fetch('assoc')) {
            return null;
        }

        return new UserEntity($row);
    }

    /**
     * Get all rows
     *
     * @return UserEntity[] Rows
     */
    public function getAll()
    {
        $query = $this->db->newQuery()
            ->select(['*'])
            ->from('users');
        $rows = $query->execute()->fetchAll('assoc');

        $result = [];
        foreach ($rows as $row) {
            $result[] = new UserEntity($row);
        }
        return $result;
    }
}
