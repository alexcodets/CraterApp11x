<?php

namespace Crater\Models;

use Auth;
use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerNote extends Model
{
    use HasFactory;

    protected $table = 'customer_notes';

    protected $guarded = [
        'id'
    ];

    protected $fillable = ['summary', 'note', 'stiky', 'company_id', 'user_id', 'creator_id'];

    protected $appends = [
        'formattedCustomerNoteDate','formattedCreatorName'
    ];

    public function getformattedCreatorNameAttribute($value)
    {

        if (is_null($this->creator_id)) {
            return "N/A";
        }

        $user = User::where("id", $this->creator_id)->first();

        if($user) {
            return $user->name;
        } else {
            return "N/A";
        }

    }

    public function scopeApplyFilters($query, array $filters)
    {

        $filters = collect($filters);


        if ($filters->get('summary')) {
            $query->whereSearch($filters->get('summary'));
        }

        if ($filters->get('note')) {
            $query->WhereTitle($filters->get('note'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'note';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    // Buscador por Summary
    public function scopeWhereSummary($query, $summary)
    {
        return $query->where('summary', 'LIKE', '%'.$summary.'%');
    }

    // Buscador por primer note
    public function scopeWhereNote($query, $note)
    {
        return $query->where('note', 'LIKE', '%'.$note.'%');
    }

    // Buscador por orden
    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    // Paginador
    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function createCustomerNote($request)
    {

        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;
        $data['creator_id'] = Auth::user()->id;
        /* $data['user_id'] = auth()->user()->id; */
        $CustomerNote = CustomerNote::create($data);

        return $CustomerNote;
    }

    /* public static function createCustomerNote($request)
    {
        $itemGroup = self::create([
            'summary' => $request->summary,
            'note'   => $request->note,
            'stiky'   => $request->stiky,
            'user_id'    => $request->header('user')
        ]);

        self::createItems($itemGroup, $request);

        return $itemGroup;
    } */

    public static function updateCustomerNote($request, $CustomerNote)
    {
        $CustomerNote->update([
            'summary' => $request->summary,
            'note' => $request->note,
            'stiky' => $request->stiky,
            'user_id' => $request->user_id
        ]);

        return $CustomerNote;
    }

    public function getFormattedCustomerNoteDateAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat." h:i:s A");
    }
}
