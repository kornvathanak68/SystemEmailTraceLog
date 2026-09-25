<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRecord extends Model
{
    protected $fillable = [
        'report_run_id',
        'pic_id',
        'message_id',
        'recipient_email',
        'company_name',
        'kam_name',
        'subject',
        'sent_at',
        'status',
        'failure_reason',
        'related_file_path',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function reportRun(): BelongsTo
    {
        return $this->belongsTo(ReportRun::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(Pic::class);
    }

    public function isFailed(): bool
    {
        return in_array(
            strtolower((string) $this->status),
            array_map('strtolower', config('delivery.failure_statuses', []))
        );
    }
}
