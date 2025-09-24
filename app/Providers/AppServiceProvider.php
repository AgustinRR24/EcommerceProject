<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Filament\Events\Auth\Registered as FilamentRegistered;
use App\Listeners\AssignRoleOnRegistration;
use Spatie\Health\Facades\Health;
//use Spatie\Health\Checks\Checks\CpuLoadCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
//use Spatie\Health\Checks\Checks\DiskSpaceCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\FlareErrorOccurrenceCountCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
//use Spatie\Health\Checks\Checks\HttpCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
//use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;



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
        Event::listen(
            Registered::class,
            AssignRoleOnRegistration::class,
        );

        Event::listen(
            FilamentRegistered::class,
            function (FilamentRegistered $event) {
                $event->getUser()->assignRole('panel_user');
            }
        );

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'es', 'pt_BR']); // Inglés y Español
        });

        Health::checks([
            //CpuLoadCheck::new()->failWhenLoadIsHigherThan(2.0),
            DatabaseCheck::new(),
            DatabaseSizeCheck::new(),
            DebugModeCheck::new(),
            //DiskSpaceCheck::new()->warnWhenUsedSpaceIsAbovePercentage(70),
            EnvironmentCheck::new(),
            FlareErrorOccurrenceCountCheck::new(), // Requiere Spatie Flare
            HorizonCheck::new(),                   // Solo si usás Laravel Horizon
            //HttpCheck::new()->url('https://example.com'), // Comprobación de un endpoint
            OptimizedAppCheck::new(),
            PingCheck::new()->url('https://google.com'),
            QueueCheck::new(),                     // Requiere queue configurado
            //RedisCheck::new(),
            ScheduleCheck::new(),
        ]);

    }
}
