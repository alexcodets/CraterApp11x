<?php

namespace Crater\Avalara\DMO;

use Crater\Avalara\DataObject\AvalaraApiDO;
use Crater\Models\AvalaraConfig;
use stdClass;

class AvalaraApiDMO
{
    public function __construct()
    {
        //$this->dataDB();
    }

    /**
     * DO functionality for when data come from config file
     * @return void
     */
    private function dataConfig(): object
    {
        $env = config('avalara.environment');
        $apiData = config("avalara.{$env}");
        $data = json_decode(json_encode($apiData)); //client_id, user_name, password, url
        $data->actions = config('avalara.actions');

        return $data;
    }

    /**
     * DO functionality for when data come from Database
     * @param AvalaraConfig $model
     * @return object
     */
    private function dataDB(AvalaraConfig $model): object
    {

        $data = new stdClass();
        $data->user_name = $model->user_name;
        $data->password = $model->password_decode;
        $data->client_id = $model->client_id;
        $data->url = $model->url;
        $data->actions = config('avalara.actions');
        $data->profile_id = $model->profile_id;

        return $data;
    }

    /**
     * Get Avalara Config Data, if no Config is selected the first Active will be choice.
     *
     * @param AvalaraConfig|null $config
     * @return AvalaraApiDO
     */
    public function getData(AvalaraConfig $config = null): AvalaraApiDO
    {
        if ($config == null) {
            $config = AvalaraConfig::active()->first();
        }

        return new AvalaraApiDO($this->dataDB($config));
    }
}
