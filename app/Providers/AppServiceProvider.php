<?php

namespace App\Providers;

use App\Helpers\Common;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Helpers\IConstants;

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
    //
    Gate::before(function ($user, $ability) {
      return $user->hasRole(IConstants::ROLE_SUPER_ADMIN) ? true : null;
    });

    View::share('settings', Common::settings());
  }
}
