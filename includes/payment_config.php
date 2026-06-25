<?php

/* PAYMENT CONFIGUARTION
MODE:
STUB - LOCAL SIMULATION (NO EXTERNAL APIS) - ACTIVE RIGHT NOW
LIVE 
*/

return [
    'mode' => 'stub',
    'currency' => 'RON',

    /* PAYMENT OPTIONS BY ORDER TYPE
    SERVICES- MANUAL ONLY 
    DIGITAL MIXED - ACTUAL METHODS APPEAR
    */

    'methods' => [
        'manual_review' => [
            'label' => 'Manual confirmation',
            'provider' => 'internal_stub',
            'enabled' => true,
            'allowed_order_types' => ['service'],
            'checkout_note' => 'Used for custom quotes, bookings, and manually confirmed services.',
        ],

        'bank_transfer' => [
            'label' => 'Bank transfer',
            'provider' => 'future_bank_transfer',
            'enabled' => true,
            'allowed_order_types' => ['digital', 'mixed'],
            'checkout_note' => 'Future method.',
        ],

        'card_gateway' => [
            'label' => 'Card payment',
            'provider' => 'future_card_gateway',
            'enabled' => true,
            'allowed_order_types' => ['digital', 'mixed'],
            'checkout_note' => 'Future method.',
        ],
    ],
];
