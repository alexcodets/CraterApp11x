<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Log;

class LogsModule extends Model
{
    use HasFactory;

    protected $table = 'logsmodule';

    protected $fillable = [
        'id',
        'module',
        'task',
        'slug',
        'task_id',
        'username',
        'useremail',
        'role',
        'company_id',
        'created_at',
        'updated_at',
        'message',

    ];

    protected $appends = [
        'formattedCreatedAt',
    ];

    /**
     * Metodo para registrar un log por modulo.
     * @param String $module nombre del modulo
     * @param String $task , el tipo de operacion: list, create, update, get, delete, stast, report
     * @param String $slug la ruta de la interfaz grafica que llama al metodo.
     * @param Int $task_id , cero (0) por default  el id de la operacion, esto aplica cuando es una operacion CRUD
     * @param String $username el nombre del usuario que hizo la operacion
     * @param String $useremail el email del usuario que hizo la operacion
     * @param String $role el role del usuario que hizo la operacion
     * @param Int $company_id , el id de la compañia asociada al usuario que hizo la operacion
     *
     * @return bool
     */
    public static function createLog($module = false, $task = false, $slug = false, $task_id = 0, $username = false, $userEmail = false, $role = false, $company_id = 0, $message = false): bool
    {

        $obj = new LogsModule();
        $obj->module = $module;
        $obj->task = $task;
        $obj->slug = $slug;
        $obj->task_id = $task_id;
        $obj->username = $username;
        $obj->useremail = $userEmail;
        $obj->role = $role;
        $obj->company_id = $company_id;

        if ($message) {
            $obj->deletemessage = $message;
        }
        $obj->save();

        if ($obj) {
            return true;
        } else {
            return false;
        }

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

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('module', 'LIKE', '%'.$term.'%')
                    ->orWhere('task', 'LIKE', '%'.$term.'%')
                    ->orWhere('username', 'LIKE', '%'.$term.'%')
                    ->orWhere('created_at', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeWhereUser($query, $name)
    {
        return $query->where('username', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereModule($query, $name)
    {
        return $query->where('module', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereTask($query, $name)
    {
        return $query->where('task', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereDate($query, $name)
    {
        return $query->where('created_at', 'LIKE', '%'.$name.'%');
    }

    public function scopeLogsBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'LogsModule.created_at',
            [$start->format('Y-m-d'), $end->format('Y-m-d ')]
        );
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('module')) {
            $query->whereModule($filters->get('module'));
        }

        if ($filters->get('task')) {
            $query->whereTask($filters->get('task'));
        }

        if ($filters->get('username')) {
            $query->whereUser($filters->get('username'));
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
