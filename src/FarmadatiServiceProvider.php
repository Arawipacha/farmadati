<?php 
namespace Farmadati;

use Farmadati\Interfaces\FarmadatiClientInterface;
use Illuminate\Support\ServiceProvider;

class FarmadatiServiceProvider extends ServiceProvider{

    public function boot(){
        //$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations'),
            __DIR__.'/app/Models/' => app_path('Models'),
            __DIR__.'/app/Http/Controllers' => app_path('Http/Controllers'),
        ], 'farmadati-migrations');
    }

    public function register()
    {
    //$this->app->bind(FarmadatiClientInterface::class, Client::class); // intrface geneirica Taxes   
    $this->app->singleton(FarmadatiClientInterface::class, function(){
       return new Client(env('FARMA_USER'),env('FARMA_PASSWORD')); 
    }); 
    //$this->app->singleton(FarmadatiClientInterface::class, Client::class);        
    }
    
}
