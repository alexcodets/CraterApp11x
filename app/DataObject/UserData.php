<?php

namespace Crater\DataObject;

use Crater\Models\User;

class UserData
{
    public string $name;

    public string $email;

    public ?string $phone;

    public ?string $id;

    public ?string $company_id;

    public ?float $balance;

    public ?string $customer_type;

    public ?string $first_name;

    public ?string $last_name;

    public ?string $customcode;

    public ?string $contact_name;

    public function __construct(
        string $name,
        string $email,
        ?string $phone,
        ?string $id,
        ?string $company_id,
        ?float $balance,
        ?string $customer_type,
        ?string $first_name,
        ?string $last_name,
        ?string $customcode,
        ?string $contact_name
    ) {

        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->id = $id;
        $this->company_id = $company_id;
        $this->balance = $balance;
        $this->customer_type = $customer_type;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->customcode = $customcode;
        $this->contact_name = $contact_name;
    }

    public static function fromModel(User $user): self
    {
        return new self(
            $user->name,
            $user->email,
            $user->phone,
            $user->id,
            $user->company_id,
            $user->balance,
            $user->customer_type,
            $user->first_name,
            $user->last_name,
            $user->customcode,
            $user->contact_name
        );

    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['id'] ?? null,
            $data['company_id'] ?? null,
            $data['balance'] ?? null,
            $data['customer_type'] ?? null,
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['customcode'] ?? null,
            $data['contact_name'] ?? null
        );

    }
}
