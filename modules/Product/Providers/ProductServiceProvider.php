<?php

declare(strict_types=1);

namespace Modules\Product\Providers;

use Illuminate\Support\Facades\Route;
use BasePackage\Shared\Module\ModuleServiceProvider;

class ProductServiceProvider extends ModuleServiceProvider
{
    public static function getModuleName(): string
    {
        return 'Product';
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
        Route::prefix('api/v1/admin/products')
            ->middleware('api')
            ->group($this->getModulePath() . '/Resources/routes/admin.php');

        Route::prefix('api/v1/customer/products')
        ->middleware('api')
        ->group($this->getModulePath() . '/Resources/routes/customer.php');
    }
}
