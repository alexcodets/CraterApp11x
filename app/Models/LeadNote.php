<?php

namespace Crater\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'body',
        'phone',
        'creator_id',
        'lead_id',
        'company_id',
        'leadnote_number',
    ];

    protected $appends = [
        'formattedUserName',
        'formattedAddeDate',
    ];

    public function getFormattedUserNameAttribute()
    {
        // Consulta el modelo User por el campo id donde id == $this->creator_id
        $user = User::find($this->creator_id);

        // Si la consulta genera un resultado, retorna el campo nombre, caso contrario retorna 'N/A'
        return $user ? $user->name : 'N/A';
    }

    public function getFormattedAddeDateAttribute()
    {
        if (is_null($this->created_at)) {
            return null;
        }

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat);
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
        $query->where('lead_notes.company_id', $company_id);
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'created_at';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }
}
