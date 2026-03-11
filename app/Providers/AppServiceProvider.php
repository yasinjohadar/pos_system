<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\WhatsAppMessageReceived;
use App\Listeners\AutoReplyWhatsAppListener;

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
        Paginator::useBootstrapFive();
        
        // تسجيل PermissionServiceProvider
        $this->app->register(PermissionServiceProvider::class);

        // Register WhatsApp auto-reply listener
        Event::listen(
            WhatsAppMessageReceived::class,
            AutoReplyWhatsAppListener::class
        );
    }
}