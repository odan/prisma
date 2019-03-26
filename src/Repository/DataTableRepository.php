<?php

namespace App\Repository;

use Cake\Database\Query;

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
    public function buildQuery(Query $query, array $params): Query
    {
        return $query;

        $sortDirection = $params['options']['sortDirection'] ?? 'asc';
        $sortProperty = gv($params['options'], 'sortProperty', '');
        $sortFlag = gv($params['options'], 'sortFlag', 'natural');
        $search = gv($params['options'], 'search', '');
        $joins = gv($params['options'], 'joins', []);

        $fields = [];
        if (!empty($params['columns'])) {
            foreach ($params['columns'] as $column) {
                if (empty($column['sortfield'])) {
                    $fieldName = $column['property'];
                } else {
                    $fieldName = $column['sortfield'];
                }
                if (!isset($fieldName)) {
                    continue;
                }

                $fields[] = $this->getFieldName($fieldName);
            }

            $query->select($fields);
        }
        if (trim($search) != '') {
            $query = $this->getConditions($query, $params);
        }

        if (!empty($sortProperty)) {
            // natural, numeric, regular, string
            if ($sortFlag == 'numeric') {
                $sortProperty = $this->getFieldName($sortProperty) . ' + 0';
            } else {
                $sortProperty = $this->getFieldName($sortProperty);
            }
            if ($sortDirection == 'asc') {
                $query->order($sortProperty);
            }
            if ($sortDirection == 'desc') {
                $query->orderDesc($sortProperty);
            }
        }

        return $query;
    }
}
