<?php

declare(strict_types=1);

namespace Modules\RateLimit\Providers;

use Illuminate\Support\Facades\Route;
use BasePackage\Shared\Module\ModuleServiceProvider;

class RateLimitServiceProvider extends ModuleServiceProvider
{
    public static function getModuleName(): string
    {
        return 'RateLimit';
    }

    public function boot(): void
    {
        $this->registerTranslations();
        //$this->registerConfig();
        $this->registerMigrations();
    }

    public function register(): void
    {
        $this->registerRoutes();
    }

    public function mapRoutes(): void
    {
        Route::prefix('api/v1/admin/rate_limits')
            ->middleware('api')
            ->group($this->getModulePath() . '/Resources/routes/api.php');
    }
}
