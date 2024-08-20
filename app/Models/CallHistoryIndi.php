<?php

namespace Crater\Models;

use Crater\Traits\ModelFormatingTime;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallHistoryIndi extends Model
{
    use HasFactory;
    use ModelPagination;
    use ModelFormatingTime;

    protected $table = 'history_call_indi';

    public function callDetailRegisterTotal(): BelongsTo
    {
        return $this->belongsTo(CallDetailRegisterTotal::class, 'call_detail_register_totals_id');
    }

    public function pbxCdrTotal(): BelongsTo
    {
        return $this->belongsTo(CallDetailRegisterTotal::class, 'call_detail_register_totals_id');
    }

    public function getDateAttribute()
    {
        return $this->created_at->toDateTimeString();
    }
}
