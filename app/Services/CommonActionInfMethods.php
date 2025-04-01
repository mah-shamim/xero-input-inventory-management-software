<?php

namespace App\Services;

class CommonActionInfMethods
{
    /**
     * @param array|string $query
     * @param $model
     * @return void
     */
    public function implementSortingQuery(array|string $query, $model): void
    {
        if ($this->isSortByAndSortOrderExists($query)) {
            $model->orderBy($query['sortBy'][0], $query['sortDesc'][0] == 'false' ? 'asc' : 'desc');
        }
    }

    /**
     * @param array|string $query
     * @return bool
     */
    public function isSortByAndSortOrderExists(array|string $query): bool
    {
        return array_key_exists('sortBy', $query) && count($query['sortBy']) && ($query['sortDesc'] && count($query['sortDesc']));
    }

}