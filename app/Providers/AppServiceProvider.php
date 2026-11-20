<?php

namespace App\Providers;

use App\Models\Borrowing;
use App\Models\Member;
use App\Policies\BorrowingPolicy;
use App\Policies\MemberPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Borrowing::class, BorrowingPolicy::class);
        Gate::policy(Member::class, MemberPolicy::class);
    }
}
