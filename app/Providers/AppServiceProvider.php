<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Client::class, \App\Policies\ClientPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\DocType::class, \App\Policies\DocTypePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\RecordingPurpose::class, \App\Policies\RecordingPurposePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\State::class, \App\Policies\StatePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\County::class, \App\Policies\CountyPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\City::class, \App\Policies\CityPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\FeeRule::class, \App\Policies\FeeRulePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\File::class, \App\Policies\FilePolicy::class);
    }
}
