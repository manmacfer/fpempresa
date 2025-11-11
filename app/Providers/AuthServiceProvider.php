<?php

namespace App\Providers;

use App\Models\Matching;
use App\Policies\MatchingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [\App\Models\Matching::class => \App\Policies\MatchingPolicy::class,];
    
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
