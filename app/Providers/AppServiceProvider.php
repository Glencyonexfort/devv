<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         if(env('APP_DEBUG')) {
            //     \Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
            //         echo '<pre style="height: 140px;display: inline-table;">';
            //         print_r(["Query"=>$query->sql,"Values"=>implode(', ', $query->bindings),"Time"=> $query->time]);
            //         echo '</pre>';
            //    });
            
            // DB::listen(function($query) {
            //     File::append(
            //         storage_path('/logs/query.log'),
            //         '{Time'.$query->time.'} ' . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
            //    );
            // });
         }

        if (env('REDIRECT_HTTPS')) {
            \URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
