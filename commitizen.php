<?php

return [
    'type' => [
        'lengthMin' => 1, // Min length of the type
        'lengthMax' => 8, // Max length of the type
        'acceptExtra' => false, // Allow adding types not listed in 'values' key
        'values' => ['feat', 'fix', 'deps', "release",], // All the values usable as type
    ],
    'scope' => [
        'lengthMin' => 0, // Min length of the scope
        'lengthMax' => 16, // Max length of the scope
        'acceptExtra' => true, // Allow adding scopes not listed in 'values' key
        'values' => ["base", "public-web", "private-api", 'deps-update', ], // All the values usable as scope
    ],
    'description' => [
        'lengthMin' => 1, // Min length of the description
        'lengthMax' => 44, // Max length of the description
    ],
    'subject' => [
        'lengthMin' => 1, // Min length of the subject
        'lengthMax' => 50, // Max length of the subject
    ],
    'body' => [
        'wrap' => 72, // Wrap the body at 72 characters
    ],
    'footer' => [
        'wrap' => 72, //Wrap the footer at 72 characters
    ],
];
