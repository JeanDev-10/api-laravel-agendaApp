<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Contact;
use App\Models\Favorite;
use App\Policies\ContactPolicy;
use App\Policies\FavoritePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Contact::class => ContactPolicy::class,
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->PoliciesFavorite();

    }
    protected function PoliciesFavorite(){
        Gate::define('delete', [FavoritePolicy::class, 'delete']);
        Gate::define('store', [FavoritePolicy::class, 'store']);
        Gate::define('show', [FavoritePolicy::class, 'show']);
    }
}
