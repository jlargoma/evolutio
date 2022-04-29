<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
   public function register()
    {
        $filenames = glob(app_path('Helpers/*.php'));

        if ($filenames !== false && is_iterable($filenames)) {
            foreach ($filenames as $filename) {
                require_once $filename;
            }
        }
    }
}
