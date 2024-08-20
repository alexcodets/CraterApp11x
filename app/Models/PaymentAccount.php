<?php

namespace Crater\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class PaymentAccount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'country_id', 'state_id', 'city', 'address_1', 'address_2', 'zip',
        'payment_account_type', 'card_number','credit_card', 'cvv', 'expiration_date', 'ACH_type', 'account_number',
        'routing_number', 'num_check', 'bank_name', 'client_id', 'company_id', 'status', 'main_account'];

    // relacion country
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // relacion state
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function scheduleLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model');
    }

    public function getPartiallyBlockCardNumberAttribute(): ?string
    {
        if ($this->decrypted_card_number != null) {
            $cardNumber = substr($this->decrypted_card_number, -4);

            return "************$cardNumber";
        }

        return null;
    }

    public function getDecryptedCardNumberAttribute(): ?string
    {
        return $this->decryptField($this->card_number);
    }

    public function getDecryptedExpirationDateAttribute(): ?Carbon
    {
        $date = $this->decryptField($this->expiration_date);

        return $date ? Carbon::createFromFormat('Y-m', $date) : null;
    }

    public function getFormattedDecryptedExpirationDateAttribute(): ?string
    {
        return $this->decrypted_expiration_date ? $this->decrypted_expiration_date->format('Y-m') : null;
    }

    public function getDecryptedCvvAttribute(): ?string
    {
        return $this->decryptField($this->cvv);
    }

    public function getDecryptedAchTypeAttribute(): ?string
    {
        return $this->decryptField($this->ACH_type);
    }

    public function getDecryptedRoutingNumberAttribute(): ?string
    {
        return $this->decryptField($this->routing_number);
    }

    public function getDecryptedAccountNumberAttribute(): ?string
    {
        return $this->decryptField($this->account_number);
    }

    public function getDecryptedBankNameAttribute(): ?string
    {
        return $this->decryptField($this->bank_name);
    }

    private function decryptField($field): ?string
    {
        return $field ? Crypt::decryptString($field) : null;
    }

    // Filtro para buscador en la vista principal
    public function scopeApplyFilters(Builder $query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('first_name')) {
            $query->WhereFirstName($filters->get('first_name'));
        }

        if ($filters->get('last_name')) {
            $query->WhereLastName($filters->get('last_name'));
        }

        if ($filters->get('payment_account_type')) {
            $query->wherePaymentAccountType($filters->get('payment_account_type'));
        }

        if ($filters->get('credit_card')) {
            return $query->where('credit_card', 'LIKE', '%'.$filters->get('credit_card').'%');
        }

        if ($filters->get('address_1')) {
            $query->whereAddress1($filters->get('address_1'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereFirstName($query, $first_name)
    {
        return $query->where('first_name', 'LIKE', '%'.$first_name.'%');
    }

    public function scopeWhereLastName($query, $last_name)
    {
        return $query->where('last_name', 'LIKE', '%'.$last_name.'%');
    }

    public function scopeWherePaymentAccountType($query, $payment_account_type)
    {
        return $query->where('payment_account_type',  $payment_account_type);
    }

    public function scopeWhereAddress1($query, $address_1)
    {
        return $query->where('address_1', 'LIKE', '%'.$address_1.'%');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('first_name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function createPaymentAccount($request)
    {
        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;
        if ($request->payment_account_type === 'CC') {
            $data['cvv'] = Crypt::encryptString($request->cvv);
            $data['card_number'] = Crypt::encryptString($request->card_number);
            //\Log::debug($request->expiration_date);
            $data['expiration_date'] = Crypt::encryptString($request->expiration_date);
            $data['credit_card'] = $request->credit_cards['value'];
        } elseif ($request->payment_account_type === 'ACH') {
            $data['ACH_type'] = Crypt::encryptString($request->ACH_type);
            $data['account_number'] = Crypt::encryptString($request->account_number);
            $data['routing_number'] = Crypt::encryptString($request->routing_number);
            $data['num_check'] = Crypt::encryptString($request->num_check);
            $data['bank_name'] = Crypt::encryptString($request->bank_name);
        }
        $payment_account = PaymentAccount::create($data);

        return $payment_account;
    }
}
