<?php

namespace Crater\Avalara\DataObject;

class AvalaraCompanyModelDO extends AvalaraCompanyDO
{
    public function __construct($model)
    {
        $this->business_class = $model->bscl;
        $this->service_class = $model->svcl;
        $this->facilities = $model->fclt;
        $this->regulated = $model->reg;
        $this->franchise = $model->frch;
        $this->identifier = $model->company_identifier;
        $this->accountReference = $model->account_reference;
    }

    public function checkItems($model): array
    {
        if (! $model->item_cdr_id) {
            return ['success' => false, 'msg' => __('avalara.error.company_model.items.required.cdr')];
        }

        if (! $model->item_did_id) {
            return ['success' => false, 'msg' => __('avalara.error.company_model.items.required.did')];
        }

        if (! $model->item_extension_id) {
            return ['success' => false, 'msg' => __('avalara.error.company_model.items.required.ext')];
        }

        /*
        if (!$model->item_international_id) {
        return ['success' => false, 'msg' => 'Id for item International Calling Is required'];
        }
         */
        return ['success' => true];
    }
}
