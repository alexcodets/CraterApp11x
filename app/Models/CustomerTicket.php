<?php

namespace Crater\Models;

use Auth;
use Cache;
use Carbon\Carbon;
use Crater\Mail\CustomerTicketsEmail;
use Crater\Traits\SendEmailsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;
use Illuminate\Support\Facades\DB;
use Log;
use Mail;

//Traits

class CustomerTicket extends Model
{
    use HasFactory;
    use SendEmailsTrait;
    // use SendEmailsTrait;

    // protected $table = 'customer_tickets';

    protected $guarded = [
        'id',
    ];

    protected $fillable = ['ticket_number', 'summary', 'note', 'dep_id', 'assigned_id', 'priority', 'status', 'company_id', 'user_id', 'creator_id', 'date', 'time', 'send_notification_customer'];

    protected $appends = [
        'formattedCustomerNoteDate',
    ];

    public $default_prioritys = [
        'E' => 'Emergency',
        'C' => 'Critical',
        'H' => 'High',
        'M' => 'Medium',
        'L' => 'Low',
    ];

    public $statusOptions = [
        'S' => 'Awaiting Staff Reply',
        'C' => 'Awaiting Client Reply',
        'I' => 'In Progress',
        'O' => 'On Hold',
        'M' => 'Completed',
    ];

    public function scopeApplyFilters($query, array $filters)
    {

        $filters = collect($filters);

        if ($filters->get('summary')) {
            $query->whereSearch($filters->get('summary'));
        }

        if ($filters->get('note')) {
            $query->WhereTitle($filters->get('note'));
        }

        if ($filters->get('user_id')) {
            $query->whereUser($filters->get('user_id'));
        }

        if ($filters->get('dep_id')) {
            $query->whereDepartament($filters->get('dep_id'));
        }

        if ($filters->get('assigned_id')) {
            $query->whereAssigned($filters->get('assigned_id'));
        }

        if ($filters->get('ticket_number')) {
            $query->where('ticket_number', 'LIKE', '%'.$filters->get('ticket_number').'%');
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            /*  $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date')); */
            $to = date($filters->get('to_date'));
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($to."+ 1 days")));

            $query->ticketsBetween($start, $end);
        }

        if ($filters->get('priority')) {
            $query->wherePriority($filters->get('priority'));
        }

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }

        if ($filters->get('status_oc')) {
            $query->whereStatusOc($filters->get('status_oc'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'note';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }

        if ($filters->get('search')) {
            $query->where(function (Builder $query) use ($filters) {
                return $query->where('ticket_number', 'LIKE', '%'.$filters->get('search').'%')
                    ->orWhere('summary', 'LIKE', '%'.$filters->get('search').'%');
            });
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

    // Buscador por customer
    public function scopeWhereUser($query, $user_id)
    {
        return $query->where('customer_tickets.user_id', $user_id);
    }

    /* Buscador por Departament */
    public function scopeWhereDepartament($query, $dep_id)
    {
        return $query->where('customer_tickets.dep_id', $dep_id);
    }

    /* Buscador por Assigned */
    public function scopeWhereAssigned($query, $assigned_id)
    {
        return $query->where('customer_tickets.assigned_id', $assigned_id);
    }

    /* Buscador por fecha inicio y fin */

    public function scopeticketsBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'customer_tickets.created_at',
            [$start->format('Y-m-d h:i:s'), $end->format('Y-m-d h:i:s')]
        );
    }

    public function scopewherePriority($query, $priority)
    {
        return $query->where('customer_tickets.priority', $priority);
    }

    public function scopewhereStatus($query, $status)
    {
        return $query->where('customer_tickets.status', $status);
    }

    public function scopewhereStatusOc($query, $status)
    {
        if ($status === 'OPEN' || $status === 'open') {
            return $query->where('customer_tickets.status', '!=', 'C');
        } elseif ($status === 'CLOSE' || $status === 'close') {
            return $query->where('customer_tickets.status', 'C');
        }
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

    public static function createCustomerTicket($request)
    {
        $data = $request->validated();
        $user = null;

        // validar si la peticion la realiza un cliente
        if ($data['assigned_id'] == null) {
            // listar usuarios asociados con el departamento seleccionado departaments
            $departament = TicketDepartament::with('users:id')->find($data['dep_id']);
            $lengthUsers = count($departament->users);
            // asignar ticket a un usuario aleatorio
            $randomUser = rand(0, $lengthUsers - 1);
            $user = $departament->users[$randomUser];
            $user = User::where("id", $user->id)->first();
            $data['assigned_id'] = $user->id;
            $data['creator_id'] = $user->id;
            $data['company_id'] = $user->company_id;
        } else {
            $user = User::where("id", $data['assigned_id'])->first();
            $data['creator_id'] = $user->id;
            $data['company_id'] = $user->company_id;
        }

        //Date and Time
        if (! isset($request['date']) && ! isset($request['time'])) {
            $data['date'] = Carbon::now()->format('Y-m-d');
            $data['time'] = Carbon::now()->format('H:i:s');
        }

        $data['ticket_number'] = $request->get("ticket_number_selected") != null
        ? $request->get("ticket_number_selected")
        : $request->get("ticket_number");

        $data['send_notification_customer'] = $request->get('send_notification_customer') ? 1 : 0;

        $customerTicket = CustomerTicket::create($data);

        if ($request->has('user_groups')) {
            self::createUserGroups($customerTicket, $request);
        }

        if ($request->filled('services')) {
            foreach ($request->services as $item) {
                $customerTicket->services()
                    ->attach(
                        $item['service_id'],
                        [
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]
                    );
            }
        }

        if ($request->filled('pbxServices')) {
            foreach ($request->pbxServices as $item) {
                $customerTicket->pbxServices()
                    ->attach(
                        $item['service_id'],
                        [
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]
                    );
            }
        }

        return $customerTicket;
    }

    public static function updateCustomerTicket($request, $customerTicket)
    {
        // validar si la peticion la realiza un cliente y si cambio de departamento
        if (Auth::user()->role == 'customer' && $customerTicket->dep_id != $request->dep_id) {
            // listar usuarios asociados con el departamento seleccionado departaments
            $departament = TicketDepartament::with('users:id')->find($request->dep_id);
            $lengthUsers = count($departament->users);
            // asignar ticket a un usuario aleatorio
            $randomUser = rand(0, $lengthUsers - 1);
            $user = $departament->users[$randomUser];
            $request->assigned_id = $user->id;
        }
        \Log::debug("updatecustomer");

        $notification = $request->input('send_notification_customer') ? 1 : 0;
        $customerTicket->update([
            'summary' => $request->summary,
            'note' => $request->note,
            'dep_id' => $request->dep_id,
            'assigned_id' => $request->assigned_id,
            'priority' => $request->priority,
            'status' => $request->status,
            'user_id' => $request->user_id,
            'send_notification_customer' => $notification,
        ]);

        // Eliminar los grupos asociados

        self::deleteItemGroups($customerTicket);

        if ($request->has('user_groups')) {
            // Asociar nuevos grupos
            self::createUserGroups($customerTicket, $request);
        }

        self::deleteServices($customerTicket);

        if ($request->filled('services')) {
            foreach ($request->services as $item) {
                $customerTicket->services()
                    ->attach(
                        $item['service_id'],
                        [
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]
                    );
            }
        }

        if ($request->filled('pbxServices')) {
            foreach ($request->pbxServices as $item) {
                $customerTicket->pbxServices()
                    ->attach(
                        $item['service_id'],
                        [
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]
                    );
            }
        }

        return $customerTicket;
    }

    public function ticketsGroups(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_ticket_users', 'ticket_id', 'item_user_id')
            ->whereNull('customer_ticket_users.deleted_at')
            ->withTimestamps();
    }

    public static function createUserGroups($item, $request)
    {
        foreach ($request->user_groups as $group) {
            $item->ticketsGroups()
                ->attach(
                    $group['item_user_id'],
                    [
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
        }
    }

    public static function deleteItemGroups($item)
    {

        foreach ($item->ticketsGroups as $group) {
            $item->ticketsGroups()->updateExistingPivot($group->id, ['deleted_at' => Carbon::now()]);
        }
    }

    public function getFormattedCustomerNoteDateAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat." h:i:s A");
    }

    // relacion de uno a muchos con la tabla ticket_departaments
    public function ticketDepartament(): BelongsTo
    {
        return $this->belongsTo(TicketDepartament::class, 'dep_id');
    }

    // relacion de uno a muchos con la tabla users indicando el cliente
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // relacion de uno a muchos con la tabla users indicando el asignado
    public function assigned(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_id');
    }

    // relacion de todos los usuario asociados a un ticket
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_ticket_users', 'ticket_id', 'item_user_id')
            ->whereNull('customer_ticket_users.deleted_at')
            ->withTimestamps();
    }

    public function sendEmail($customerTicket, $mode = 'create')
    {
        try {
            $customer = User::where("id", $customerTicket->user_id)->first();

            $departament = $customerTicket->ticketDepartament;
            $assigned = $customerTicket->assigned;
            $titlecreatecus = 'Ticket Notice ';
            $titlecreateuse = 'Ticket Notice ';

            if ($mode === 'create') {
                $templateEmail = CompanySetting::getSetting('ticket_creatio_customer', $customerTicket->company_id);
                $templateEmailUser = CompanySetting::getSetting('ticket_creatio_user', $customerTicket->company_id);
                $titlecreatecus = CompanySetting::getSetting('ticket_creatio_customer_subject', $customerTicket->company_id);
                $titlecreateuse = CompanySetting::getSetting('ticket_creatio_user_subject', $customerTicket->company_id);

            } elseif ($mode === 'update') {
                $templateEmail = CompanySetting::getSetting('ticket_update_customer', $customerTicket->company_id);
                $templateEmailUser = CompanySetting::getSetting('ticket_update_user', $customerTicket->company_id);
                $titlecreatecus = CompanySetting::getSetting('ticket_update_customer_subject', $customerTicket->company_id);
                $titlecreateuse = CompanySetting::getSetting('ticket_update_user_subject', $customerTicket->company_id);

            }
            $add = Address::whereNULL("user_id")
                ->join("countries", "countries.id", "=", "addresses.country_id")
                ->join("states", "states.id", "=", "addresses.state_id")
                ->select("countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
                ->first();

            if ($add == null) {
                $add = Address::first()
                    ->join("countries", "countries.id", "=", "addresses.country_id")
                    ->join("states", "states.id", "=", "addresses.state_id")
                    ->select("countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
                    ->first();
            }

            $company = Company::where("id", $customerTicket->company_id)->first();

            $array = [];
            $array["PRIMARY_CONTACT_NAME"] = $customer->name;
            $array["CONTACT_DISPLAY_NAME"] = $customer->contact_name;
            $array["CONTACT_EMAIL"] = $customer->email;
            $array["CONTACT_PHONE"] = $customer->phone;
            $array["CONTACT_WEBSITE"] = $customer->website;
            $array["COMPANY_NAME"] = $company->name;
            $array["COMPANY_COUNTRY"] = $add->country;
            $array["COMPANY_STATE"] = $add->state;
            $array["COMPANY_CITY"] = $add->city;
            $array["STATE_CODE"] = $add->state_code;
            $array["COMPANY_ADDRESS_STREET_1"] = $add->address_street_1;
            $array["COMPANY_ADDRESS_STREET_2"] = $add->address_street_2;
            $array["COMPANY_PHONE"] = $add->phone;
            $array["COMPANY_ZIP_CODE"] = $add->zip;
            $array["TICKECT_DEPARTAMENT"] = $departament->name;
            $array["TICKECT_ASSIGNED_TO"] = $assigned->name;
            $array["TICKECT_PRIORITY"] = $this->default_prioritys[$customerTicket->priority];
            $array["TICKECT_STATUS"] = $this->statusOptions[$customerTicket->status];
            $array["TICKECT_DETAILS"] = $customerTicket->note;
            $array["TICKECT_NUMBER"] = $customerTicket->ticket_number;
            // str_replace
            $newMessage = $templateEmail;
            $newMessageUser = $templateEmailUser;
            foreach ($array as $key => $value) {
                $newMessage = str_replace("{".$key."}", $value, $newMessage);
                $newMessageUser = str_replace("{".$key."}", $value, $newMessageUser);
                $titlecreatecus = str_replace("{".$key."}", $value, $titlecreatecus);
                $titlecreateuse = str_replace("{".$key."}", $value, $titlecreateuse);
            }
            $emailsSend = [$customer->email, $assigned->email];
            $data['company'] = $company;

            $data['PRIMARY_COLOR'] = $this->getPrimaryColor($company->id);

            $titlecreatecus = $this->removeAttributesHtml($titlecreatecus);
            $titlecreateuse = $this->removeAttributesHtml($titlecreateuse);

            if (! $customerTicket->send_notification_customer) {
                Mail::to($customer->email)->send(new CustomerTicketsEmail($titlecreatecus, $newMessage, $data));
                \Log::debug("entro a customet tickets");
            }

            try {

                Mail::to($assigned->email)->send(new CustomerTicketsEmail($titlecreateuse, $newMessageUser, $data));
            } catch (\Throwable $th) {
                Log::error($th);
            }
            // correo bbc
            $bbcmail = CompanySetting::select('value', 'id')->where('option', 'ticket_bbc_email')->where('company_id', $company->id)->first();

            if ($bbcmail != null) {
                if ($bbcmail->value != "") {
                    try {
                        Mail::to($bbcmail->value)->send(new CustomerTicketsEmail($titlecreatecus, $newMessage, $data));
                    } catch (\Throwable $th) {
                        Log::error($th);
                    }

                }

            }

            $setting = CompanySetting::select('id')->where('option', 'estimate_auto_generate')->where('company_id', $company->id)->first();
            $mailable_id = $setting->id;

            // envio de contactos
            if (! $customerTicket->send_notification_customer) {
                $contacts = Contacts::select('email')->where('customer_id', $customer->id)->where('allow_receive_emails', 1)->where('email_tickets', 1)->get();
                foreach ($contacts as $key => $email) {
                    if ($email != null && $email != '') {
                        // enviar email
                        try {
                            Mail::to($email)->send(new CustomerTicketsEmail($titlecreatecus, $newMessage, $data));
                            // save emails logs
                            $this->saveEmailLog($email, $titlecreatecus, $newMessage, $mailable_id, $company->id, $customer->id);
                        } catch (\Throwable $th) {
                            Log::error($th);
                        }
                    }
                }
            }

            // enviar correo a usuarios
            $listUser = User::where('role', 'super admin')->where('email_invoices', 1)->where('company_id', $customerTicket->company_id)->get();

            foreach ($listUser as $user) {
                try {
                    Mail::to($user->email)->send(new CustomerTicketsEmail($titlecreatecus, $newMessage, $data));
                } catch (\Throwable $th) {
                    Log::error($th);
                }
            }

            // save emails logs
            // $emailTrait = new SendEmailsTrait;
            $this->saveEmailLog($customer->email, $titlecreatecus, $newMessage, $mailable_id, $company->id, $customer->id);
            $this->saveEmailLog($assigned->email, $titlecreateuse, $newMessageUser, $mailable_id, $company->id, $customer->id);

        } catch (\Throwable $th) {
            \Log::debug($th);
        }

    }

    public function removeAttributesHtml($string)
    {
        $temp = str_replace('<p>', '', $string);
        $temp = str_replace('</p>', '', $temp);
        $temp = str_replace('<strong>', '', $temp);
        $temp = str_replace('</strong>', '', $temp);
        $temp = str_replace('<em>', '', $temp);
        $temp = str_replace('</em>', '', $temp);
        $temp = str_replace('<s>', '', $temp);
        $temp = str_replace('</s>', '', $temp);
        $temp = str_replace('<u>', '', $temp);
        $temp = str_replace('</u>', '', $temp);
        $temp = str_replace('<code>', '', $temp);
        $temp = str_replace('</code>', '', $temp);
        $temp = str_replace('<h1>', '', $temp);
        $temp = str_replace('</h1>', '', $temp);
        $temp = str_replace('<h2>', '', $temp);
        $temp = str_replace('</h2>', '', $temp);
        $temp = str_replace('<h3>', '', $temp);
        $temp = str_replace('</h3>', '', $temp);
        $temp = str_replace('<ul>', '', $temp);
        $temp = str_replace('</ul>', '', $temp);
        $temp = str_replace('<li>', '', $temp);
        $temp = str_replace('</li>', '', $temp);
        $temp = str_replace('<ol>', '', $temp);
        $temp = str_replace('</ol>', '', $temp);
        $temp = str_replace('<blockquote>', '', $temp);
        $temp = str_replace('</blockquote>', '', $temp);
        $temp = str_replace('<pre>', '', $temp);
        $temp = str_replace('</pre>', '', $temp);

        return $temp;
    }

    public function getPrimaryColor($company_id = null)
    {
        if (isset($company_id)) {
            $colorInvoice = CompanySetting::getSetting('color_invoice', $company_id);

            return $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';

        }

    }

    public function services(): MorphedByMany
    {
        return $this->morphedByMany(CustomerPackage::class, 'service', 'service_tickets', 'customer_ticket_id', 'service_id')->whereNull('service_tickets.deleted_at');
    }

    public function pbxServices(): MorphedByMany
    {
        return $this->morphedByMany(PbxServices::class, 'service', 'service_tickets', 'customer_ticket_id', 'service_id')->whereNull('service_tickets.deleted_at');
    }

    public static function deleteServices($customerTicket)
    {
        $customerTicketId = $customerTicket->id;

        // Realizar la consulta raw para marcar los registros como eliminados
        DB::table('service_tickets')
            ->where('customer_ticket_id', $customerTicketId)
            ->update(['deleted_at' => now()]);
    }

    public static function getNextTicketNumber($value)
    {
        // Get the last created order
        $lastOrder = CustomerTicket::where('ticket_number', 'LIKE', $value.'-%')

            ->orderBy('ticket_number', 'desc')
            ->first();

        if (! $lastOrder) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode("-", $lastOrder->ticket_number);
            $number = $number[1];
        }

        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf('%06d', intval($number) + 1);
    }
}
