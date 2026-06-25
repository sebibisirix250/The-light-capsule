<?php

/* MAIL CONFIGURATION
MODE:
DISABLED
LOG  - LOCAL - ACTIVE ON THIS MODE
SMTP - FOR DOMAIN IMPLEMENTATION
*/

return [
    'mode' => 'log',

    'from_email' => 'no-reply@thelightcapsule.com',
    'from_name'  => 'The Light Capsule',

    'admin_email'    => 'admin@thelightcapsule.com',
    'orders_email'   => 'orders@thelightcapsule.com',
    'bookings_email' => 'bookings@thelightcapsule.com',
    'log_dir' => __DIR__ . '/../storage/emails/',

    //INACTIVE
    'smtp' => [
        'host'       => '',
        'port'       => 587,
        'username'   => '',
        'password'   => '',
        'encryption' => 'tls', // TLS/SSL
    ],
];
