<?php

return [
    "guard_name" => env("APIGUARD_NAME", "apiguard"),
    "api_url" => env("APIGUARD_URL", ""),
    "forwarded_headers" => explode(",", env("APIGUARD_HEADERS", "Authorization,X-COMPANY-ID")),
    "user_column" => env("APIGUARD_USER_COLUMN", ""),
    "user_model" => "\\App\\Models\\User",
    "post_data" => env("APIGUARD_POSTDATA", json_encode([]))
];