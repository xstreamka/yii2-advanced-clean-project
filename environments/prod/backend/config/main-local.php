<?php

return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
            'csrfCookie' => [
                'httpOnly' => true,
                'secure' => true,
            ],
        ],
        'user' => [
            'identityCookie' => [
                'httpOnly' => true,
                'secure' => true,
            ],
        ],
        'session' => [
            'cookieParams' => [
                'httpOnly' => true,
                'secure' => true,
            ],
        ],
    ],
];
