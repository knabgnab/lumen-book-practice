<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;

class FractalServiceProvider extends ServiceProvider
{
    public function register()
    {
        Log::info("FractalServiceProvider");
        // Bind the DataArraySerializer to an interface contract
        $this->app->bind(
            'League\Fractal\Serializer\SerializerAbstract',
            'League\Fractal\Serializer\DataArraySerializer'
        );
        $this->app->bind(FractalResponse::class, function ($app) {
            $manager = new Manager();
            $serializer = $app['League\Fractal\Serializer\SerializerAbstract'];
            return new FractalResponse($manager, $serializer);
        });
        $this->app->alias(FractalResponse::class, 'fractal');
    }
}
