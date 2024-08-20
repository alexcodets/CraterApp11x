<?php

namespace Crater\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketDepartament extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'client_permission', 'email', 'sender_override', 'send_emails', 'automatically_transition_admin' ,'default_priority', 'email_handling', 'automatically_close', 'automatically_delete', 'status','company_id', 'creator_id','schedule_data','receive_tickets_emails','receive_mobile_tickets_emails','receive_tickets_messenger_notifications'];

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('name')) {
            $query->whereName($filters->get('name'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {

            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';

            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';

            $query->whereOrder($field, $orderBy);
        }

        if ($filters->get('containsUsers')) {
            // return departments that contain the users
            $query->whereHas('users', function ($query) {
                $query->where('user_id', '<>', null);
            });
        }
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeWhereName($query, $name)
    {
        return $query->where(function ($query) use ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%')
            ->orWhere('description', 'LIKE', '%'.$name.'%')
            ->orWhere('email', 'LIKE', '%'.$name.'%');
        });
        ;
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        return $query->orderBy($orderByField, $orderBy);
    }

    public function scopePaginateData($query, $limit)
    {

        // if ($limit == 'all') {
        //     return collect(['data' => $query->get()]);
        // }

        return $query->paginate($limit);
    }

    // Creando proveedor
    public static function createDepartament($request)
    {
        $data['name'] = $request->name;
        $data['schedule_data'] = $request->schedule_data;
        $data['receive_tickets_emails'] = $request->receive_tickets_emails;
        $data['receive_mobile_tickets_emails'] = $request->receive_mobile_tickets_emails;
        $data['receive_tickets_messenger_notifications'] = $request->receive_tickets_messenger_notifications;
        $data['description'] = $request->description;
        $data['client_permission'] = $request->client_permission;
        $data['email'] = $request->email;
        $data['sender_override'] = $request->sender_override;
        $data['send_emails'] = $request->send_emails;
        $data['automatically_transition_admin'] = $request->automatically_transition_admin;
        $data['default_priority'] = $request->default_priority;
        $data['email_handling'] = $request->email_handling;
        $data['automatically_close'] = $request->automatically_close;
        $data['automatically_delete'] = $request->automatically_delete;
        $data['status'] = $request->status;
        $data['company_id'] = Auth::user()->company_id;
        $data['creator_id'] = Auth::user()->id;

        $departament = TicketDepartament::create($data);

        if ($request->users) {
            self::createUsers($departament, $request);
        }

        return $departament;
    }

    public static function deleteTicketDepartament($ids)
    {

        $ticketd = self::find($ids);

        LogsModule::createLog(
            "Ticket Departament",
            "delete",
            "admin/tickets/departaments/delete",
            $ids,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "TicketDepartament: "
        );

        $ticketd->delete();

        return true;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'departament_users', 'dep_group_id', 'user_id')
            ->whereNull('departament_users.deleted_at')
            ->withTimestamps();
    }

    public static function createUsers($departament, $request)
    {

        foreach ($request->users as $user) {
            $departament->users()
                ->attach(
                    $user['user_id'],
                    [
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]
                );
        }
    }

    public function updateUsers($request)
    {
        // Eliminar los grupos asociados
        self::deleteDepartamentGroups($this);

        if ($request->has('users')) {
            // Asociar nuevos grupos
            self::createUsers($this, $request);
        }
    }

    public static function deleteDepartamentGroups($dep)
    {
        foreach ($dep->users as $group) {
            $dep->users()->updateExistingPivot($group->id, ['deleted_at' => Carbon::now()]);
        }
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(CustomerTicket::class, 'dep_id');
    }
}
