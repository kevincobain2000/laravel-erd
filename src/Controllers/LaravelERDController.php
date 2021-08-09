<?php

namespace Kevincobain2000\LaravelERD\Controllers;

use Route;
use Closure;
use Kevincobain2000\LaravelERD\LaravelERD;
use Illuminate\Routing\Controller;

class LaravelERDController extends Controller
{
    private $laravelERD;

    public function __construct(LaravelERD $laravelERD)
    {
        $this->laravelERD = $laravelERD;
    }

    public function index()
    {
        $docs = [];
        return view('erd::index')->with(compact('docs'));
    }

}
