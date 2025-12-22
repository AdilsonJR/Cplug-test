<?php

return [
    'pruneOldInventory' =>[
        'days' => env('INVENTORY_PRUNE_DAYS', 90),
        'at' => env('INVENTORY_PRUNE_AT', '02:00'),
    ]
];
