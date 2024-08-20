<?php

namespace Crater\Avalara\DataObject;

class AvalaraApiDO
{
    public string $user_name;

    public string $password;

    public string $url;

    public array $actions;

    public $profile_id;

    public $client_id;

    public function __construct(Object $var = null)
    {
        $this->user_name = $var->user_name;
        $this->password = $var->password;
        $this->client_id = $var->client_id;
        $this->url = $var->url;
        $this->actions = $var->actions;
        $this->profile_id = $var->profile_id;
    }
}
