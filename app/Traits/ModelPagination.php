<?php

namespace Crater\Traits;

use Illuminate\Support\Facades\Schema;

trait ModelPagination
{
    public function scopePaginateData($query, string $default = 'all')
    {
        $limit = $this->getLimit($default);
        if ($limit === 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);

    }

    public function scopePaginateOrNot($query)
    {
        $limit = $this->getLimit('all');

        if ($this->isValidNumber($limit)) {
            return $query->paginate($limit);
        }

        return $query->get();
    }

    public function scopeOrderData($query, string $table, string $default = 'start_date', string $order = 'desc')
    {
        return $query->orderBy($this->getOrderBy($table, $default), $this->getOrder($order));
    }

    public function scopeOrderRelatedData($query, string $table, string $default = 'start_date', string $order = 'desc')
    {
        return $query->orderBy($this->getOrderByRelated($table, $default), $this->getOrder($order));
    }

    private function getLimit($default)
    {
        $limit = request()->input('limit');
        if (is_numeric($limit) && is_int(intval($limit))) {
            return $limit;
        }

        return $default;

    }

    private function getOrderBy(string $table, string $default)
    {
        $orderBy = request()->input('order_by', $default);
        if (! Schema::hasColumn($table, $orderBy)) {
            $orderBy = $default;
        }

        return $orderBy;

    }

    private function getOrder(string $default = 'desc')
    {
        $order = request()->input('order', $default);
        if (in_array($order, ['asc', 'desc'])) {
            return $order;
        }

        return 'desc';

    }

    public function getOrderByRelated(string $table, string $default)
    {
        $orderBy = request()->input('order_by', $default);
        if (! Schema::hasColumn($table, $orderBy)) {
            $orderBy = "{$table}.{$default}";
        }

        return "{$table}.{$orderBy}";
    }
}
