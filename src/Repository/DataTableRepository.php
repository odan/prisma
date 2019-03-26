<?php

namespace App\Repository;

use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query;
use RuntimeException;

/**
 * Repository.
 */
class DataTableRepository extends BaseRepository
{
    /**
     * Load data table items.
     *
     * @param Query $query the query
     * @param array $params the parameters
     *
     * @return array the table data
     */
    public function load(Query $query, array $params): array
    {
        $query = $this->buildQuery($query, $params);

        $countQuery = clone $query;
        $countQuery->select(['count' => $countQuery->func()->count('*')], true);
        $countRows = $countQuery->execute()->fetchAll('assoc');

        $count = 0;
        foreach ($countRows ?: [] as $countRow) {
            $count += $countRow['count'] ?: 0;
        }

        $draw = (int)($params['draw'] ?? 1);
        $offset = (int)($params['start'] ?? 1);
        $limit = (int)($params['length'] ?? 10);
        $offset = ($offset < 0 || empty($count)) ? 0 : $offset;

        $query->offset($offset);
        $query->limit($limit);

        return [
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'draw' => $draw,
            'data' => $query->execute()->fetchAll('assoc') ?: [],
        ];
    }

    /**
     * Returns datatable filter as query array.
     *
     * https://datatables.net/manual/server-side
     *
     * @param array $params params
     * @param Query $query
     *
     * @return Query Query
     */
    protected function buildQuery(Query $query, array $params): Query
    {
        $order = (array)($params['order'] ?? []);
        $searchValue = trim($params['search']['value'] ?? '');
        $columns = (array)($params['columns'] ?? []);
        $table = (string)$query->clause('from')[0];
        $fields = $this->getTableFields($table);

        if ($searchValue !== '') {
            $orConditions = [];

            foreach ($columns as $columnItem) {
                $searchField = (string)$columnItem['data'];
                $searchField = $this->getFieldName($table, $searchField, $fields);
                $orConditions[$searchField . ' LIKE'] = '%' . $this->escapeLike($searchValue) . '%';
            }

            $query->andWhere(function (QueryExpression $exp) use ($orConditions) {
                return $exp->or_($orConditions);
            });
        }

        if (!empty($order)) {
            foreach ($order as $orderItem) {
                $columnIndex = $orderItem['column'];
                $columnName = $columns[$columnIndex]['data'];
                $columnName = $this->getFieldName($table, $columnName, $fields);
                $dir = $orderItem['dir'];

                if ($dir === 'asc') {
                    $query->order($columnName);
                }
                if ($dir === 'desc') {
                    $query->orderDesc($columnName);
                }
            }
        }

        return $query;
    }

    /**
     * Escape like string.
     *
     * @param string $value the string to escape for a like query
     *
     * @throws RuntimeException
     *
     * @return string the escaped string
     */
    protected function escapeLike(string $value): string
    {
        $result = str_replace(['%', '_'], ['\%', '\_'], $value);

        if (!is_string($result)) {
            throw new RuntimeException('Escaping query failed');
        }

        return $result;
    }

    /**
     * Get query field name.
     *
     * @param string $table table name
     * @param string $field field name
     * @param array $fields table fields
     *
     * @return string full field name
     */
    protected function getFieldName(string $table, string $field, array $fields): string
    {
        if (isset($fields[$field]) && strpos($field, '.') === false) {
            $field = "$table.$field";
        }

        return $field;
    }

    /**
     * Get table fields.
     *
     * @param string $table Table name
     *
     * @throws RuntimeException
     *
     * @return array Fields
     */
    protected function getTableFields(string $table): array
    {
        $query = $this->newSelect('information_schema.columns');
        $query->select(['column_name', 'data_type', 'character_maximum_length']);
        $query->andWhere([
            'table_schema' => $query->newExpr('DATABASE()'),
            'table_name' => $table,
        ]);

        $rows = $query->execute()->fetchAll('assoc');
        if (empty($rows)) {
            throw new RuntimeException(__('Columns not found in table: %s', $table));
        }

        $result = [];
        foreach ($rows as $row) {
            $result[$row['column_name']] = $row;
        }

        return $result;
    }
}
