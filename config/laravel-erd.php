<?php

return [
    'url' => 'erd',
    'middlewares' => [
        //Example
        //\App\Http\Middleware\NotFoundWhenProduction::class,
    ],
    'namespace'        => 'App\Models\\',
    'models_path'      => base_path('app/Models'),
    'docs_path'        => base_path('docs/erd/')
];
