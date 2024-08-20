<?php

namespace Crater\Traits;

use Carbon\Carbon;
use Crater\Models\Address;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Country;
use Crater\Models\FileDisk;
use Crater\Models\State;
use Crater\Models\User;
use Illuminate\Support\Facades\App;
use Log;
use Request;

trait GeneratesPdfTrait
{
    public function getGeneratedPDFOrStream($collection_name)
    {

        // Primero, verificamos si $this->company_id es null
        if (is_null($this->company_id)) {
            // Buscamos el usuario por $this->user_id
            $user = User::find($this->user_id);
            // Si encontramos el usuario, asignamos su company_id a $this->company_id
            if ($user) {
                $this->company_id = $user->company_id;
                // Guardamos el cambio en el objeto
                $this->save();
            }
        }

        // Luego, verificamos si $this->user es null
        if (is_null($this->user)) {
            // Realizamos la búsqueda por el campo creator_id en el modelo User
            $user = User::where('creator_id', $this->creator_id)->first();
            // Si encontramos un usuario, asignamos su company_id a $this->company_id
            if ($user) {
                $this->company_id = $user->company_id;
                // Guardamos el cambio en el objeto
                $this->save();
            }
        }
        $pdf = $this->getGeneratedPDF($collection_name);
        Log::debug("+++++++++++++++++Generated PDF", ["pdf" => $pdf]);
        if ($pdf && file_exists($pdf['path'])) {
            Log::debug("+++++++++++++++++Generated PDF", ["pdf" => $pdf]);

            return response()->make(file_get_contents($pdf['path']), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$pdf['file_name'].'.pdf"',
            ]);
        }

        $locale = CompanySetting::getSetting('language', $this->company_id);
        Log::debug("+++++++++++++++++Generated Locale", ["locale" => $locale]);
        App::setLocale($locale);

        $pdf = $this->getPDFData();
        Log::debug("+++++++++++++++++Generated PDF2", ["pdf" => $pdf]);

        return response()->make($pdf->stream(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$this[$collection_name.'_number'].'.pdf"',
        ]);
    }

    public function getGeneratedPDF($collection_name)
    {
        try {
            $media = $this->getMedia($collection_name)->first();

            if ($media) {
                $file_disk = FileDisk::find($media->custom_properties['file_disk_id']);

                if (! $file_disk) {
                    return false;
                }

                $file_disk->setConfig();

                $path = null;

                if ($file_disk->driver == 'local') {
                    $path = $media->getPath();
                } else {
                    $path = $media->getTemporaryUrl(Carbon::now()->addMinutes(5));
                }

                return collect([
                    'path' => $path,
                    'file_name' => $media->file_name,
                ]);
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    public function generatePDF(string $collection_name, string $file_name, bool $deleteExistingFile = false)
    {
        $save_pdf_to_disk = CompanySetting::getSetting('save_pdf_to_disk', $this->company_id);

        if ($save_pdf_to_disk == 'NO') {
            return 0;
        }

        $locale = CompanySetting::getSetting('language', $this->company_id);

        App::setLocale($locale);

        $pdf = $this->getPDFData();

        \Storage::disk('local')->put('temp/'.$collection_name.'/'.$this->id.'/temp.pdf', $pdf->output());

        if ($deleteExistingFile) {
            $this->clearMediaCollection($collection_name);
        }

        $file_disk = FileDisk::whereSetAsDefault(true)->first();

        if ($file_disk) {
            $file_disk->setConfig();
        }

        $media = \Storage::disk('local')->path('temp/'.$collection_name.'/'.$this->id.'/temp.pdf');

        try {
            $this->addMedia($media)
                ->withCustomProperties(['file_disk_id' => $file_disk->id])
                ->usingFileName($file_name.'.pdf')
                ->toMediaCollection($collection_name, config('filesystems.default'));

            \Storage::disk('local')->deleteDirectory('temp/'.$collection_name.'/'.$this->id);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getFormattedString($format, $invoice = null)
    {
        //Log::debug($invoice);;
        $values = array_merge($this->getFieldsArray($invoice), $this->getExtraFields());

        $str = nl2br(strtr($format, $values));

        $str = preg_replace('/{(.*?)}/', '', $str);

        $str = preg_replace("/<[^\/>]*>([\s]?)*<\/[^>]*>/", '', $str);

        $str = str_replace("<p>", "", $str);

        $str = str_replace("</p>", "</br>", $str);

        return $str;
    }

    public function getFieldsArray($invoice = null)
    {
        $customer = $this->user;
        $shippingAddress = $customer->shippingAddress ?? new Address();
        $billingAddress = $customer->billingAddress ?? new Address();
        $companyAddress = $this->company->address ?? new Address();
        $companyAddress->state = State::find($companyAddress->state_id);
        $companyAddress->country = Country::find($companyAddress->country_id);

        //Log::debug($companyAddress);

        if ($invoice != null) {
            $direccion = Address::where("id", $invoice)->first();
            if ($direccion != null) {
                //Log::debug($direccion);
                $billingAddress = $direccion;
            }
        }

        $state = "";
        $state2 = "";
        if ($billingAddress->state_id != null) {
            $obj = State::where("id", $billingAddress->state_id)->first();
            if ($obj != null) {
                $state = $obj->name;
            }
        }

        if ($shippingAddress->state_id != null) {
            $obj = State::where("id", $shippingAddress->state_id)->first();
            if ($obj != null) {
                $state2 = $obj->name;
            }
        }

        $fields = [
            '{SHIPPING_ADDRESS_NAME}' => $shippingAddress->name,
            '{SHIPPING_COUNTRY}' => $shippingAddress->country_name,
            '{SHIPPING_STATE}' => $state2,
            '{SHIPPING_CITY}' => $shippingAddress->city,
            '{SHIPPING_ADDRESS_STREET_1}' => $shippingAddress->address_street_1,
            '{SHIPPING_ADDRESS_STREET_2}' => $shippingAddress->address_street_2,
            '{SHIPPING_PHONE}' => $shippingAddress->phone,
            '{SHIPPING_ZIP_CODE}' => $shippingAddress->zip,
            '{BILLING_ADDRESS_NAME}' => $billingAddress->name,
            '{BILLING_COUNTRY}' => $billingAddress->country_name,
            '{BILLING_STATE}' => $state,
            '{BILLING_CITY}' => $billingAddress->city,
            '{BILLING_ADDRESS_STREET_1}' => $billingAddress->address_street_1,
            '{BILLING_ADDRESS_STREET_2}' => $billingAddress->address_street_2,
            '{BILLING_PHONE}' => $billingAddress->phone,
            '{BILLING_ZIP_CODE}' => $billingAddress->zip,
            '{COMPANY_NAME}' => $this->company->name,
            '{COMPANY_COUNTRY}' => $companyAddress->country->name ?? null,
            '{COMPANY_STATE}' => $companyAddress->state->name ?? null,
            '{STATE_CODE}' => $companyAddress->state->code ?? null,
            '{COMPANY_CITY}' => $companyAddress->city,
            '{COMPANY_ADDRESS_STREET_1}' => $companyAddress->address_street_1,
            '{COMPANY_ADDRESS_STREET_2}' => $companyAddress->address_street_2,
            '{COMPANY_PHONE}' => $companyAddress->phone,
            '{COMPANY_ZIP_CODE}' => $companyAddress->zip,
            '{CONTACT_DISPLAY_NAME}' => $customer->name,
            '{PRIMARY_CONTACT_NAME}' => $customer->contact_name,
            '{PRIMARY_COLOR}' => $this->getPrimaryColor($this->company->id),
            '{CONTACT_EMAIL}' => $customer->email,
            '{CONTACT_PHONE}' => $customer->phone,
            '{CONTACT_WEBSITE}' => $customer->website,
            '{CUSTOMER_LOGIN}' => Request::root().'/login',

        ];

        $customFields = $this->fields;
        $customerCustomFields = $this->user->fields;

        foreach ($customFields as $customField) {
            $fields['{'.$customField->customField->slug.'}'] = $customField->defaultAnswer;
        }

        foreach ($customerCustomFields as $customField) {
            $fields['{'.$customField->customField->slug.'}'] = $customField->defaultAnswer;
        }

        return $fields;
    }

    public function getPrimaryColor($company_id = null)
    {
        if (isset($company_id)) {
            $colorInvoice = CompanySetting::getSetting('color_invoice', $company_id);

            return $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
        }

    }
}
