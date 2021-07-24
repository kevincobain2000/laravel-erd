<?php

namespace Kevincobain2000\LaravelERD;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Kevincobain2000\LaravelERD\Commands\LaravelERDCommand;

class LaravelERDServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-erd')
            ->hasConfigFile('laravel-erd')
            ->hasCommand(LaravelERDCommand::class);
    }
}
