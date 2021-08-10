<?php

namespace Kevincobain2000\LaravelERD;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kevincobain2000\LaravelERD\LaravelERD
 */
class LaravelERDFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-erd';
    }
}
