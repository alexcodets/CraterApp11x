<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'formattedCreatedAt',
    ];

    public function mailable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        return Carbon::parse($this->created_at)->format('Y-m-d H:m:s');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('from', 'LIKE', '%'.$term.'%')
                    ->orWhere('subject', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeWhereEmail($query, $email)
    {
        return $query->where('to', 'LIKE', '%'.$email.'%');
    }

    public function scopeWhereSubject($query, $subject)
    {
        return $query->where('subject', 'LIKE', '%'.$subject.'%');
    }

    public function scopeLogsBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'email_logs.created_at',
            [$start->format('Y-m-d'), $end->format('Y-m-d ')]
        );
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('email')) {
            $query->whereEmail($filters->get('email'));
        }

        if ($filters->get('subject')) {
            $query->whereSubject($filters->get('subject'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->logsBetween($start, $end);
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }
}
