<?php

namespace Crater\Http\Requests;

class GenericIndexRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'limit' => 'bail|nullable|integer|min:2|max:100',
            'sort' => 'bail|nullable|string',
        ];
    }

    //TODO: sort is a array (string using | as separator)
    public function getSort(array $fields): array
    {
        $sort = $this->validated()['sort'] ?? '-created_at';

        if (! in_array($sort, $fields)) {
            $sort = '-created_at';
        }

        $order = $sort[0] === '-' ? 'desc' : 'asc';
        if ($order === 'desc') {
            $sort = substr($sort, 1);
        }

        return compact('sort', 'order');

    }
}
