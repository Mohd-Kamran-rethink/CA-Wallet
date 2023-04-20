<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

use App\Setting;
use App\User;
class SettingDataProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('Admin.index', function ($view) {
            $settings = Setting::first();
            $user = session('user');
            $userData = null;
            if ($user) {
                $userData = User::find($user->id);
            }
            $view->with([
                'settings' => $settings,
                'userData' => $userData
            ]);
        });
    }
}
