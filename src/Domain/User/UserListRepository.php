<?php

namespace App\Domain\User;

use App\Repository\BaseRepository;
use App\Repository\DataTableRepository;
use Cake\Database\Connection;

/**
 * Repository.
 */
class UserListRepository extends BaseRepository
{
    /**
     * @var DataTableRepository
     */
    private $dataTable;

    /**
     * Constructor.
     *
     * @param Connection $connection
     * @param DataTableRepository $dataTableRepository The repository
     */
    public function __construct(Connection $connection, DataTableRepository $dataTableRepository)
    {
        parent::__construct($connection);
        $this->dataTable = $dataTableRepository;
    }

    /**
     * Insert new user.
     *
     * @param array $params The user
     *
     * @return array The table data
     */
    public function getTableData(array $params): array
    {
        $query = $this->newSelect('users');
        $query->select(['users.*']);

        return $this->dataTable->load($query, $params);
    }
}
