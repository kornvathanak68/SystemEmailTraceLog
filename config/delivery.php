<?php

return [
    'incoming_path' => storage_path('app/reports/incoming'),
    'output_path'   => storage_path('app/reports/output'),

    'columns' => [
        'sent_at'         => ['DATE TIME', 'date_time'],
        'status'          => ['EVENT ID', 'event_id'],
        'subject'         => ['EMAIL SUBJECT', 'message_cubject'],
        'failure_reason'  => ['RECICENT STATUS', 'recipent_status'],
        'recipient_email' => ['SEND_ADDR', 'recipient_address'],
        'company_name'    => ['COMPANY NAME'],
        'kam_name'        => ['KAM Name'],
    ],

    'failure_statuses' => ['FAIL'],
    'subject_filter'   => 'Smart Postpaid Invoice',
];
