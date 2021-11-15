<?php

namespace App\Providers;

use App\Services\Localization\LocalizationService;
use Illuminate\Support\ServiceProvider;
use Route;

class ModularProvider extends ServiceProvider {

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        //

        $modules = config('modular.modules');
        $path = config('modular.path');

        if ($modules) {
            Route::group([
                'prefix' => LocalizationService::locale() //мультиязычные маршруты
                    ], function () use ($modules, $path) {
                        //ключ Parent module value Child  module
                        foreach ($modules as $mod => $submodules) {
                            foreach ($submodules as $key => $sub) {

                                $relativePath = "/$mod/$sub"; //сохраняем путь к дочернему модулю Родитель/Дочерний модуль


                                Route::middleware('web')
                                        ->group(function () use ($mod, $sub, $relativePath, $path) {
                                            $this->getWebRoutes($mod, $sub, $relativePath, $path);
                                        });

                                Route::prefix('api')
                                        ->middleware('api')
                                        ->group(function () use ($mod, $sub, $relativePath, $path) {
                                            $this->getApiRoutes($mod, $sub, $relativePath, $path);
                                        });
                            }
                        }
                    });
        }

        $this->app['view']->addNamespace('Pub', base_path() . '/resources/views/Pub');
        $this->app['view']->addNamespace('Admin', base_path() . '/resources/views/Admin');
    }
    /** 
     * 
     * метод для формирования Веб маршрута
     */
    private function getWebRoutes($mod, $sub, $relativePath, $path) {

        $routesPath = $path . $relativePath . '/Routes/web.php';
        if (file_exists($routesPath)) {
            //если  имя модуля не совпадает со строкой которая располагается в конфиге 
            if ($mod != config('modular.groupWithoutPrefix')) {
                Route::group(
                        [
                            'prefix' => strtolower($mod),
                            'middleware' => $this->getMiddleware($mod)
                        ],
                        function () use ($mod, $sub, $routesPath) {
                            Route::namespace("App\Modules\\$mod\\$sub\Controllers")->
                                    group($routesPath);
                        }
                );
            } else {
                Route::namespace("App\Modules\\$mod\\$sub\Controllers")->// определяем namespace
                        middleware($this->getMiddleware($mod))->
                        group($routesPath);
            }
        }
    }
    /** 
     * метод для формирования API маршрута
     * 
     * */
    private function getApiRoutes($mod, $sub, $relativePath, $path) {
        $routesPath = $path . $relativePath . '/Routes/api.php';// путь к маршруту
        if (file_exists($routesPath)) {
            Route::group(
                    [
                        'prefix' => strtolower($mod),//имя можуля
                        'middleware' => $this->getMiddleware($mod, 'api')
                    ],
                    function () use ($mod, $sub, $routesPath) {
                        Route::namespace("App\Modules\\$mod\\$sub\Controllers")-> //namespace чтобюы не прописывать в контрлоллерах
                                group($routesPath);
                    }
            );
        }
    }

    private function getMiddleware($mod, $key = 'web') {
        $middleware = [];

        $config = config('modular.groupMidleware');// считываем и конфига
        //проверяем есть ли в конфиге элемент с ключом $mod(Родительский модуль)
        if (isset($config[$mod])) {
            if (array_key_exists($key, $config[$mod])) {
                $middleware = array_merge($middleware, $config[$mod][$key]); // если ключ есть то перезаписываем посрдникб путем слияния массивов
            }
        }

        return $middleware;
    }

}
