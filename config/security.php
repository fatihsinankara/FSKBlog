<?php

return [
    'snippet_allowed_hosts' => array_values(array_filter(array_map(
        fn (string $host) => strtolower(trim($host)),
        explode(',', (string) env('SNIPPET_ALLOWED_HOSTS', ''))
    ))),
];
