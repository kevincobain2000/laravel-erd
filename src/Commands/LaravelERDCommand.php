<?php

namespace Kevincobain2000\LaravelERD\Commands;

use Illuminate\Console\Command;
use File;
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
        $modelsPath      = config('laravel-erd.models_path') ?? base_path('app/Models');
        $destinationPath = config('laravel-erd.docs_path') ?? base_path('docs/erd/');

        // extract data
        $linkDataArray = $this->laravelERD->getLinkDataArray($modelsPath);
        $nodeDataArray = $this->laravelERD->getNodeDataArray($modelsPath);

        if (! File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        File::put($destinationPath . '/index.html',
            view('erd::index')
                ->with([
                    'routingType' => config('laravel-erd.display.routing') ?? 'AvoidsNodes',

                    // pretty print array to json
                    'docs' => json_encode(
                        [
                            "link_data" => $linkDataArray,
                            "node_data" => $nodeDataArray,
                        ]
                    ),
                ])
                ->render()
        );

        $this->info("ERD data written successfully to $destinationPath");

        return 0;
    }
}
