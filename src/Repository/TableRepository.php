<?php

namespace App\Repository;

/**
 * Repository.
 */
class TableRepository implements RepositoryInterface
{
    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * Constructor.
     *
     * @param QueryFactory $queryFactory the query factory
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }

    /**
     * Fetch row by id.
     *
     * @param string $table Table name
     * @param int $id ID
     *
     * @return array Result set
     */
    public function fetchById(string $table, int $id): array
    {
        return $this->queryFactory->newSelect($table)
            ->select('*')
            ->where(['id' => $id])
            ->execute()
            ->fetch('assoc') ?: [];
    }

    /**
     * Fetch all rows.
     *
     * @param string $table Table name
     *
     * @return array Result set
     */
    public function fetchAll(string $table): array
    {
        return $this->queryFactory->newSelect($table)->select('*')->execute()->fetchAll('assoc') ?: [];
    }

    /**
     * Check if the given ID exists in the table.
     *
     * @param string $table Table name
     * @param int|string $id the ID
     *
     * @return bool True if the id exists
     */
    public function existsById(string $table, $id): bool
    {
        return $this->queryFactory->newSelect($table)
            ->select('id')
            ->andWhere(['id' => $id])
            ->execute()
            ->fetch('assoc') ? true : false;
    }
}
