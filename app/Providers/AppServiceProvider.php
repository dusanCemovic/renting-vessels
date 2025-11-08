<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Global Blade directive to format datetimes in Slovenia timezone with desired format
        Blade::directive('slDate', function ($expression) {
            return "<?php echo \\Carbon\\Carbon::parse($expression)->setTimezone('Europe/Ljubljana')->format('j F Y, H:i'); ?>";
        });
    }
}
