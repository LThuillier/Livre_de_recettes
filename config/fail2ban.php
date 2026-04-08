<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fail2Ban Applicatif
    |--------------------------------------------------------------------------
    |
    | Ce mécanisme bloque temporairement une IP après plusieurs échecs
    | d'authentification dans une fenêtre de temps donnée.
    |
    */

    'enabled' => env('FAIL2BAN_ENABLED', true),

    // Nombre d'échecs autorisés avant bannissement.
    'max_attempts' => (int) env('FAIL2BAN_MAX_ATTEMPTS', 5),

    // Fenêtre d'observation (en minutes).
    'find_time_minutes' => (int) env('FAIL2BAN_FIND_TIME_MINUTES', 10),

    // Durée du bannissement (en minutes).
    'ban_minutes' => (int) env('FAIL2BAN_BAN_MINUTES', 3),
];
