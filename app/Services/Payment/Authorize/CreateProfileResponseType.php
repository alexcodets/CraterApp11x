<?php

namespace Crater\Services\Payment\Authorize;

use Crater\Services\Payment\Authorize\Util\Mapper;
use DateTime;
use JsonSerializable;
use net\authorize\api\contract\v1\MessagesType;

/**
 * Class representing CreateProfileResponseType
 *
 *
 * XSD Type: createProfileResponse
 */
class CreateProfileResponseType implements JsonSerializable
{
    private ?MessagesType $messages = null;

    private ?string $customerProfileId = null;

    private ?array $customerPaymentProfileIdList = null;

    private ?array $customerShippingAddressIdList = null;

    public function getMessages(): ?MessagesType
    {
        return $this->messages;
    }

    public function setMessages(MessagesType $messages): CreateProfileResponseType
    {
        $this->messages = $messages;

        return $this;
    }

    public function getCustomerProfileId(): ?string
    {
        return $this->customerProfileId;
    }

    public function setCustomerProfileId(string $customerProfileId): CreateProfileResponseType
    {
        $this->customerProfileId = $customerProfileId;

        return $this;
    }

    /**
     * Adds as numericString
     *
     * @param string $numericString
     * @return self
     */
    public function addToCustomerPaymentProfileIdList(string $numericString): CreateProfileResponseType
    {
        $this->customerPaymentProfileIdList[] = $numericString;

        return $this;
    }

    public function issetCustomerPaymentProfileIdList($index): bool
    {
        return isset($this->customerPaymentProfileIdList[$index]);
    }

    public function unsetCustomerPaymentProfileIdList($index)
    {
        unset($this->customerPaymentProfileIdList[$index]);
    }

    public function getCustomerPaymentProfileIdList(): ?array
    {
        return $this->customerPaymentProfileIdList;
    }

    /**
     * Sets a new customerPaymentProfileIdList
     *
     * @param string $customerPaymentProfileIdList
     * @return self
     */
    public function setCustomerPaymentProfileIdList(array $customerPaymentProfileIdList): CreateProfileResponseType
    {
        $this->customerPaymentProfileIdList = $customerPaymentProfileIdList;

        return $this;
    }

    public function addToCustomerShippingAddressIdList(string $numericString): CreateProfileResponseType
    {
        $this->customerShippingAddressIdList[] = $numericString;

        return $this;
    }

    /**
     * isset customerShippingAddressIdList
     *
     * @param scalar $index
     * @return bool
     */
    public function issetCustomerShippingAddressIdList($index): bool
    {
        return isset($this->customerShippingAddressIdList[$index]);
    }

    /**
     * unset customerShippingAddressIdList
     *
     * @param scalar $index
     * @return void
     */
    public function unsetCustomerShippingAddressIdList($index)
    {
        unset($this->customerShippingAddressIdList[$index]);
    }

    /**
     * Gets as customerShippingAddressIdList
     *
     * @return string[]
     */
    public function getCustomerShippingAddressIdList(): ?array
    {
        return $this->customerShippingAddressIdList;
    }

    /**
     * Sets a new customerShippingAddressIdList
     *
     * @param string $customerShippingAddressIdList
     * @return self
     */
    public function setCustomerShippingAddressIdList(array $customerShippingAddressIdList): CreateProfileResponseType
    {
        $this->customerShippingAddressIdList = $customerShippingAddressIdList;

        return $this;
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
        foreach ($values as $key => $value) {
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

    // Json Set Code

    /**
     * @throws \Exception
     */
    public function set($data)
    {
        if (is_array($data) || is_object($data)) {
            $mapper = Mapper::Instance();
            foreach ($data as $key => $value) {
                $classDetails = $mapper->getClass(get_class(), $key);

                if ($classDetails !== null) {
                    if ($classDetails->isArray) {
                        if ($classDetails->isCustomDefined) {
                            foreach ($value as $keyChild => $valueChild) {
                                $type = new $classDetails->className();
                                $type->set($valueChild);
                                $this->{'addTo'.$key}($type);
                            }
                        } elseif ($classDetails->className === 'DateTime' || $classDetails->className === 'Date') {
                            foreach ($value as $keyChild => $valueChild) {
                                $type = new DateTime($valueChild);
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
                            $type = new DateTime($value);
                            $this->{'set'.$key}($type);
                        } else {
                            $this->{'set'.$key}($value);
                        }
                    }
                }
            }
        }
    }
}
