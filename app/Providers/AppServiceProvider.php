<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Student;
use App\Models\Personnel;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'student' => Student::class,
            'personnel' => Personnel::class,
        ]);

        $systemSettings = Schema::hasTable('system_settings')
            ? SystemSetting::current()
            : SystemSetting::make(SystemSetting::defaults());

        $this->app->instance('systemSettings', $systemSettings);
        View::share('systemSettings', $systemSettings);

        Config::set(
            'app.timezone',
            $systemSettings->timezone ?: 'Asia/Manila'
        );

        date_default_timezone_set(
            $systemSettings->timezone ?: 'Asia/Manila'
        );
    }
}
