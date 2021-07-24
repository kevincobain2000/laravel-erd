<?php

namespace Kevincobain2000\LaravelERD\Commands;

use Illuminate\Console\Command;
use File;
use Schema;
use ReflectionClass;
use ReflectionMethod;
use Throwable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Kevincobain2000\LaravelERD\LaravelERD;

class LaravelERDCommand extends Command
{
    public $signature = 'erd:generate';

    public $description = 'Generate ERD files';

    protected $laravelERD;

    public function __construct(LaravelERD $laravelERD)
    {
        $this->laravelERD = $laravelERD;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $namespace       = config('laravel-erd.namespace') ?? 'App\Models\\';
        $modelsPath      = config('laravel-erd.models_path') ?? base_path('app/Models');
        $destinationPath = config('laravel-erd.destination_path') ?? base_path('docs/erd/');
        $sourcePath      = __DIR__ . "/../../assets/erd/";

        $this->copyAssets($sourcePath, $destinationPath);

        // extract data
        $linkDataArray = $this->laravelERD->getLinkDataArray($namespace, $modelsPath);
        $nodeDataArray = $this->laravelERD->getNodeDataArray($namespace, $modelsPath);

        // pretty print array to json
        $erdData = json_encode(
            [
                "link_data" => $linkDataArray,
                "node_data" => $nodeDataArray,
            ],
            JSON_PRETTY_PRINT
        );

        File::put(base_path('docs/erd/erd.json'), $erdData);

        $this->info("ERD data written successfully to $destinationPath");

        return 0;
    }

    public function copyAssets(string $sourcePath, string $destinationPath)
    {
        if (! File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        File::copy($sourcePath . "/erd.js",     $destinationPath . "/erd.js");
        File::copy($sourcePath . "/index.html", $destinationPath . "/index.html");
    }
}

