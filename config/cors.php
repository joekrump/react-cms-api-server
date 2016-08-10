<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => false,
    'allowedOrigins' => ['*'],
    // 'allowedOrigins' => ['http://localapp:3000', 'http://localhost:3000', 'http://localhost:8000'],
    'allowedHeaders' => ['Origin', 'Content-Type', 'Accept', 'Authorization', 'Access-Control-Allow-Origin'],
    'allowedMethods' => ['PUT', 'POST', 'GET', 'DELETE'],
    'exposedHeaders' => [],
    'maxAge' => 0,
    'hosts' => [],
];

