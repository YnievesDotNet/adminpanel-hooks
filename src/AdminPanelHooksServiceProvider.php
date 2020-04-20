<?php

namespace YnievesDotNet\AdminPanelHooks;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Larapack\Hooks\Events\Setup;
use Larapack\Hooks\HooksServiceProvider;
use YnievesDotNet\AdminPanel\Facades\AdminPanel;

class AdminPanelHooksServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $configPath = dirname(__DIR__).'/publishable/config/adminpanel-hooks.php';

        $this->mergeConfigFrom($configPath, 'adminpanel-hooks');

        // Register the HooksServiceProvider
        $this->app->register(HooksServiceProvider::class);

        if (!$this->enabled()) {
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [$configPath => config_path('adminpanel-hooks.php')],
                'adminpanel-hooks-config'
            );
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'adminpanel-hooks');
    }

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function boot(Dispatcher $events)
    {
        if (!$this->enabled()) {
            return;
        }

        if (config('adminpanel-hooks.add-route', true)) {
            $events->listen('adminpanel.admin.routing', [$this, 'addHookRoute']);
        }

        if (config('adminpanel-hooks.add-hook-menu-item', true)) {
            $events->listen(Setup::class, [$this, 'addHookMenuItem']);
        }

        if (config('adminpanel-hooks.add-hook-permissions', true)) {
            $events->listen(Setup::class, [$this, 'addHookPermissions']);
        }

        if (config('adminpanel-hooks.publish-vendor-files', true)) {
            $events->listen(Setup::class, [$this, 'publishVendorFiles']);
        }
    }

    public function addHookRoute($router)
    {
        $namespacePrefix = '\\YnievesDotNet\\AdminPanelHooks\\Controllers\\';

        $router->get('hooks', ['uses' => $namespacePrefix.'HooksController@index', 'as' => 'hooks']);
        $router->get('hooks/{name}/enable', ['uses' => $namespacePrefix.'HooksController@enable', 'as' => 'hooks.enable']);
        $router->get('hooks/{name}/disable', ['uses' => $namespacePrefix.'HooksController@disable', 'as' => 'hooks.disable']);
        $router->get('hooks/{name}/update', ['uses' => $namespacePrefix.'HooksController@update', 'as' => 'hooks.update']);
        $router->post('hooks', ['uses' => $namespacePrefix.'HooksController@install', 'as' => 'hooks.install']);
        $router->delete('hooks/{name}', ['uses' => $namespacePrefix.'HooksController@uninstall', 'as' => 'hooks.uninstall']);
    }

    public function addHookMenuItem()
    {
        $menu = AdminPanel::model('Menu')::where('name', 'admin')->first();

        if (is_null($menu)) {
            return;
        }

        $parentId = null;

        $toolsMenuItem = AdminPanel::model('MenuItem')::where('menu_id', $menu->id)
            ->where('title', 'Tools')
            ->first();

        if ($toolsMenuItem) {
            $parentId = $toolsMenuItem->id;
        }

        $menuItem = AdminPanel::model('MenuItem')::firstOrNew([
            'menu_id' => $menu->id,
            'title'   => 'Hooks',
            'url'     => '',
            'route'   => 'adminpanel.hooks',
        ]);

        if (!$menuItem->exists) {
            $menuItem->fill([
                'target'     => '_self',
                'icon_class' => 'adminpanel-hook',
                'color'      => null,
                'parent_id'  => $parentId,
                'order'      => 13,
            ])->save();
        }
    }

    public function addHookPermissions()
    {
        AdminPanel::model('Permission')::firstOrCreate([
            'key'        => 'browse_hooks',
            'table_name' => null,
        ]);
    }

    public function publishVendorFiles()
    {
        Artisan::call('vendor:publish', ['--provider' => static::class]);
    }

    public function enabled()
    {
        if (config('adminpanel-hooks.enabled', true)) {
            return config('hooks.enabled', true);
        }

        return config('adminpanel-hooks.enabled', true);
    }
}
