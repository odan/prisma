<?php

namespace App\Model\Table;

use App\Model\Entity\UserEntity;
use Zend\Hydrator\ClassMethods as Hydrator;

/**
 * User
 */
class UserTable extends BaseTable
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

        if(!$row = $query->execute()->fetch('assoc')) {
            return null;
        }

        $hydrator = new Hydrator();
        return $hydrator->hydrate((array)$row, new UserEntity());
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

        $hydrator = new Hydrator();
        $result = [];
        foreach ($rows as $row) {
            $result[] = $hydrator->hydrate($row, new UserEntity());
        }
        return $result;
    }
}
