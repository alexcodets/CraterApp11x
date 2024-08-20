<?php

namespace Crater\Services\Payment\Authorize;

use Crater\Services\Payment\Authorize\Util\Mapper;
use Illuminate\Support\Facades\Log;

class TransactionResponse extends ANetApiResponseType
{
    private ?TransactionResponseType $transactionResponse = null;

    private ?CreateProfileResponseType $profileResponse = null;

    public function getTransactionResponse(): ?TransactionResponseType
    {
        return $this->transactionResponse;
    }

    public function setTransactionResponse(TransactionResponseType $transactionResponse): TransactionResponse
    {
        $this->transactionResponse = $transactionResponse;

        return $this;
    }

    public function getProfileResponse(): ?CreateProfileResponseType
    {
        return $this->profileResponse;
    }

    public function setProfileResponse(CreateProfileResponseType $profileResponse): TransactionResponse
    {
        $this->profileResponse = $profileResponse;

        return $this;
    }

    // Json Set Code
    public function set($data)
    {
        Log::channel('authorize')->info('Inside Set');

        if (! is_array($data) && ! is_object($data)) {
            Log::channel('authorize')->info('Going out, wront type of data');

            return;
        }

        $mapper = Mapper::Instance();
        foreach ($data as $key => $value) {
            $classDetails = $mapper->getClass(get_class(), $key);
            Log::channel('authorize')->info('ClassDetail');
            /*Log::channel('authorize')->info($classDetails);
            Log::channel('authorize')->info($key);
            Log::channel('authorize')->info(get_class());*/


            if (is_null($classDetails)) {
                continue;
            }
            Log::channel('authorize')->info('Is not null class details');

            if ($classDetails->isArray) {
                if ($classDetails->isCustomDefined) {
                    foreach ($value as $keyChild => $valueChild) {
                        $type = new $classDetails->className();
                        $type->set($valueChild);
                        $this->{'addTo'.$key}($type);
                    }
                } elseif ($classDetails->className === 'DateTime' || $classDetails->className === 'Date') {
                    foreach ($value as $keyChild => $valueChild) {
                        $type = new \DateTime($valueChild);
                        $this->{'addTo'.$key}($type);
                    }
                } else {
                    foreach ($value as $keyChild => $valueChild) {
                        $this->{'addTo'.$key}($valueChild);
                    }
                }
            } else {
                if ($classDetails->isCustomDefined) {
                    $type = new $classDetails->className();
                    $type->set($value);
                    $this->{'set'.$key}($type);
                } elseif ($classDetails->className === 'DateTime' || $classDetails->className === 'Date') {
                    $type = new \DateTime($value);
                    $this->{'set'.$key}($type);
                } else {
                    $this->{'set'.$key}($value);
                }
            }
        }
    }

    // Json Serialize Code
    public function jsonSerialize()
    {
        $values = array_filter(
            (array)get_object_vars($this),
            function ($val) {
                return ! is_null($val);
            }
        );
        $mapper = Mapper::Instance();
        foreach($values as $key => $value) {
            $classDetails = $mapper->getClass(get_class(), $key);
            if (isset($value)) {
                if ($classDetails->className === 'Date') {
                    $dateTime = $value->format('Y-m-d');
                    $values[$key] = $dateTime;
                } elseif ($classDetails->className === 'DateTime') {
                    $dateTime = $value->format('Y-m-d\TH:i:s\Z');
                    $values[$key] = $dateTime;
                }
                if (is_array($value)) {
                    if (! $classDetails->isInlineArray) {
                        $subKey = $classDetails->arrayEntryname;
                        $subArray = [$subKey => $value];
                        $values[$key] = $subArray;
                    }
                }
            }
        }

        return $values;
    }
}
