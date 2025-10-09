<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\PaymentRequestCreated::class => [
            [\App\Listeners\LogApprovalHistory::class, 'handleCreated'],
        ],
        \App\Events\PaymentRequestUpdated::class => [
            [\App\Listeners\LogApprovalHistory::class, 'handleUpdated'],
        ],
        \App\Events\PaymentRequestSubmitted::class => [
            [\App\Listeners\LogApprovalHistory::class, 'handleSubmitted'],
        ],
        \App\Events\PaymentRequestApproved::class => [
            [\App\Listeners\LogApprovalHistory::class, 'handleApproved'],
        ],
        \App\Events\PaymentRequestRejected::class => [
            [\App\Listeners\LogApprovalHistory::class, 'handleRejected'],
        ],
        \App\Events\PaymentRequestCancelled::class => [
            [\App\Listeners\LogApprovalHistory::class, 'handleCancelled'],
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
