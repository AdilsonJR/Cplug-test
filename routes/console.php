<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('seed:local-products', function () {
    if (!app()->environment('local')) {
        $this->error('Este comando só pode ser executado no ambiente local.');
        return 1;
    }

    $this->call('db:seed', [
        '--class' => 'Database\\Seeders\\LocalProductsSeeder',
    ]);

    $this->info('Local products criado.');
})->purpose('Seed de produtos mock para ambiente local');
