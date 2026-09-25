<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportRun extends Model
{
    protected $fillable = [
        'period',
        'source_file',
        'fillable_file',
        'stage',
        'status',
        'total_rows',
        'failed_count',
        'error',
    ];
    public function deliveryRecords(): HasMany
    {
        return $this->hasMany(DeliveryRecord::class);
    }
   public function failedRecords(): HasMany
{
    return $this->hasMany(DeliveryRecord::class)
        ->whereIn('status', config('delivery.failure_statuses', [
            'failed', 'bounced', 'rejected', 'undeliverable', 'deferred', 'expired',
        ]));
}
}
