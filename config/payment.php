<?php

return [
    'bank' => [
        'name'           => env('PAY_BANK_NAME', 'BCA'),
        'account'        => env('PAY_BANK_ACCOUNT', '1234567890'),
        'account_name'   => env('PAY_BANK_ACCOUNT_NAME', 'KOST APP'),
        'swift_code'     => env('PAY_BANK_SWIFT_CODE', 'BCAINIDJA'),
    ],
];
