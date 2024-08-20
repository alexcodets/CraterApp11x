<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoteTicket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'subject',
        'message',
        'reference',
        'user_id',
        'customer_ticket_id',
        'date',
        'time',
        'id',
        'public'
    ];

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        \Log::debug($filters);
        if($filters->get('reference')) {
            $query->whereReference($filters->get('reference'));
        }
        if($filters->get('subject')) {
            $query->whereSubject($filters->get('subject'));
        }
        if($filters->get('user')) {
            $query->whereUser($filters->get('user'));
        }
        if($filters->get('public')) {
            $query->wherePublic($filters->get('public'));
        }
        if ($filters->get('from_date') && $filters->get('to_date')) {
            $to = date($filters->get('to_date'));
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($to."+ 1 days")));

            $query->notesBetween($start, $end);
        }
        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'created_at';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereReference($query, $reference)
    {
        return $query->where('reference', 'LIKE', '%'.$reference.'%');
    }

    public function scopeWhereSubject($query, $subject)
    {
        return $query->where('subject', 'LIKE', '%'.$subject.'%');
    }

    public function scopeWhereUser($query, $user)
    {
        return $query->where('user_id',  $user);
    }

    public function scopewherePublic($query, $public)
    {
        return $query->where('public',  $public);
    }

    public function scopeNotesBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'note_tickets.created_at',
            [$start->format('Y-m-d h:i:s'), $end->format('Y-m-d h:i:s')]
        );
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
