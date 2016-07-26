<?php

return [

  'test' => [
    'sk' => env('STRIPE_TEST_SK', 'set me!')
  ],
  'live' =>
  [
    'sk' => env('STRIPE_LIVE_SK', 'set me!')
  ]
];