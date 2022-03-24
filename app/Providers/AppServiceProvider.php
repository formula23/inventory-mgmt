<?php

namespace App\Providers;

use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
//        View::composer('*', function ($view) {
//            $view->with('auth', Auth::user());
//        });

//        View::creator('*', function($view) {
//            $view->with('categories', Cache::remember('categories', 60, function() {
//                return Category::where('is_active', 1)->get();
//            }));
//        });

        if ($request->server->has('HTTP_X_ORIGINAL_HOST')) {

            $this->app['url']->forceRootUrl('http://'.$request->server->get('HTTP_X_ORIGINAL_HOST'));
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->isLocal()) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }

        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);

    }
}
