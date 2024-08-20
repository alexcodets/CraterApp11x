<?php

namespace Crater\Http\Controllers;

use Carbon\Carbon;
use Crater\Mail\SimpleMail;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerTicket;
use Crater\Models\NoteTicket;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;

class NoteTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['user_id'] = auth()->user()->id;
            $note_tickets = NoteTicket::create($data);

            $this->sendNotesEmail($note_tickets->id);

            return response()->json([
                'success' => true,
                'message' => 'Note created successful'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => 'error creating the note '
            ]);
        }
    }

    public static function sendNotesEmail($id)
    {
        try {
            // Note
            $note = NoteTicket::where("id", $id)->first();

            // users related to the ticket
            $customer_ticket_users = DB::table('customer_ticket_users')
                                    ->where('ticket_id', $note->customer_ticket_id)
                                    ->where("deleted_at", null)
                                    ->pluck('item_user_id')->toArray();

            // User assigned to ticket
            $user_assigned_to_ticket = CustomerTicket::where("id", $note->customer_ticket_id)
                                                    ->pluck('assigned_id')
                                                    ->toArray();

            // Users with option "ticket email" == true
            $users_email_tickets = User::where("email_tickets", 1)->pluck('id')->toArray();

            // Email BBC ticket
            $ticket_bbc_email = CompanySetting::where("option", "ticket_bbc_email")->first('value');

            // Merge Users (Para que no existan usuarios repetidos al momento de enviar el correo)
            $users_ids = array_merge($customer_ticket_users, $user_assigned_to_ticket, $users_email_tickets);
            $users_ids = array_unique($users_ids);

            // Montando el array de "Correos" pertenecientes a dichos users a los que se le enviara el MAIL
            $array_emails = [];
            foreach ($users_ids as $id) {
                $email_user = User::where("id", $id)->first('email');
                array_push($array_emails, $email_user->email);
            }
            array_push($array_emails, $ticket_bbc_email->value);

            // Formatting the mail
            $ticket = CustomerTicket::find($note->customer_ticket_id);

            $subject = 'Note # ('.$ticket->ticket_number.') - '.$note->subject;
            $body = $note->message;
            $company = Company::first();

            // Send Emails
            foreach ($array_emails as $mail) {
                Mail::to($mail)->send(new SimpleMail($subject, $body, $company));
            }

        } catch (\Throwable $th) {
            \Log::debug($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\NoteTicket  $noteTicket
     * @return \Illuminate\Http\Response
     */
    public function show(NoteTicket $noteTicket)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $noteTicket,
                'message' => 'Show note of the ticket '
            ]);
        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Note of the ticket not found '
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\NoteTicket  $noteTicket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NoteTicket $noteTicket)
    {
        try {
            $data = $request->all();
            \Log::debug($data);
            NoteTicket::where('id', $data['id'])
                ->update([
                    'subject' => $data['subject'],
                    'message' => $data['message'],
                    'public' => $data['public'],
                    'user_id' => auth()->user()->id,
                    'updated_at' => Carbon::now()->format('Y-m-d')
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Note created successful'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => 'error creating the note '
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\NoteTicket  $noteTicket
     * @return \Illuminate\Http\Response
     */
    public function destroy(NoteTicket $noteTicket)
    {
        //
    }

    public function getNotesTickets(Request $request)
    {

        $limit = $request->has('limit') ? $request->limit : 10;
        $ticket_id = $request->ticket_id;
        $notes = NoteTicket::applyFilters($request->only([
            'reference',
            'subject',
            'user',
            'from_date',
            'to_date',
            'public',
            'orderByField',
            'orderBy'

        ]))
            ->select(
                'note_tickets.id as note_id',
                'note_tickets.reference as reference',
                'note_tickets.subject as subject',
                'note_tickets.message as message',
                'note_tickets.created_at as created_at',
                'note_tickets.date as date',
                'note_tickets.time as time',
                'note_tickets.public as public',
                'users.name as user_name',
                'users.id as user_id',
            )
            ->join('users', 'note_tickets.user_id', '=', 'users.id')
            ->where('customer_ticket_id', $ticket_id)->paginateData($limit);

        return response()->json([
            'status' => 'success',
            'notes' => $notes
        ]);
    }

    public function delete(Request $request)
    {
        try {
            $data = $request->all();
            $result = NoteTicket::where('id', $data['id'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successful'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => 'error deleting the note '
            ]);
        }
    }
}
