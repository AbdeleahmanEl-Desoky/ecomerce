<?php

declare(strict_types=1);

namespace Modules\Order\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use BasePackage\Shared\Module\ModuleServiceProvider;
use Modules\Order\Models\Order;
use Modules\Order\Observers\OrderObserver;
use Modules\Order\Events\OrderCreated;
use Modules\Order\Events\OrderCancelled;
use Modules\Order\Listeners\HandleOrderCreated;
use Modules\Order\Listeners\HandleOrderCancelled;

class OrderServiceProvider extends ModuleServiceProvider
{
    public static function getModuleName(): string
    {
        return 'Order';
    }

    public function boot(): void
    {
        $this->registerTranslations();
        //$this->registerConfig();
        $this->registerMigrations();
        $this->registerObservers();
        $this->registerEventListeners();
    }

    public function register(): void
    {
        $this->registerRoutes();
    }

    public function mapRoutes(): void
    {
        Route::prefix('api/v1/admin/orders')
            ->middleware('api')
            ->group($this->getModulePath() . '/Resources/routes/api.php');

    }
    
    /**
     * Register model observers
     */
    protected function registerObservers(): void
    {
        Order::observe(OrderObserver::class);
    }
    
    /**
     * Register event listeners
     */
    protected function registerEventListeners(): void
    {
        Event::listen(OrderCreated::class, HandleOrderCreated::class);
        Event::listen(OrderCancelled::class, HandleOrderCancelled::class);
    }
}
