# Laravel ERD Generator

Automatically generate interactive ERD from Models relationships in Laravel.
- This package provides a CLI to automatically generate interactive ERD by looking into relationships setup inside Models.
- The tool's interface is available to get JSON data of relationships and table schema from Models to be used by visual charts libraries of your choice such as d3.js etc
- Output is an interactive ERD (Entity Relation Diagram) powered by HTML and JS (GOJS).


| Lang    | Link                                                                                                                                   |
| :------ | :------------------------------------------------------------------------------------------------------------------------------------- |
| Details | [Medium Article](https://medium.com/web-developer/laravel-automatically-generate-interactive-erd-from-eloquent-relations-83fe65440716) |
| Demo    | [Online Demo](https://kevincobain2000.github.io/laravel-blog/erd/)                                                                     |


## Requirements

| Lang    | Version    |
| :------ | :--------- |
| PHP     | 7.4 or 8.0 |
| Laravel | 6.* or 8.* |

## Installation

You can install the package via composer:

```bash
composer require kevincobain2000/laravel-erd --dev
```


You can publish the config file with:

```bash
php artisan vendor:publish --provider="Kevincobain2000\LaravelERD\LaravelERDServiceProvider"
```

## Usage

You can access the ERD in ``localhost:3000/erd``

or generate a static HTML

```php
php artisan erd:generate
```

ERD HTML is generated inside ``docs/``.

### Sample

#### Screenshot

![Image](https://i.imgur.com/tYk1CuC.png)

#### Get JSON output

```php
use Kevincobain2000\LaravelERD\LaravelERD;

$modelsPath = base_path('app/Models');

$laravelERD = new LaravelERD();
$linkDataArray = $laravelERD->getLinkDataArray($modelsPath);
$nodeDataArray = $laravelERD->getNodeDataArray($modelsPath);
$erdData = json_encode(
    [
        "link_data" => $linkDataArray,
        "node_data" => $nodeDataArray,
    ],
    JSON_PRETTY_PRINT
);
var_dump($erdData);
```

Sample JSON output

```js
{
    "link_data": [
        {
            "from": "comments",
            "to": "users",
            "fromText": "1..1\nBT",
            "toText": "",
            "fromPort": "author_id",
            "toPort": "id",
            "type": "BelongsTo"
        },
        {
            "from": "comments",
            "to": "posts",
            "fromText": "1..1\nBT",
            "toText": "",
            "fromPort": "post_id",
            "toPort": "id",
            "type": "BelongsTo"
        },
        ...
        ...
    ],
    "node_data": [
        {
            "key": "comments",
            "schema": [
                {
                    "name": "id",
                    "isKey": true,
                    "figure": "Hexagon",
                    "color": "#be4b15",
                    "info": "integer"
                },
                {
                    "name": "author_id",
                    "isKey": false,
                    "figure": "Decision",
                    "color": "#6ea5f8",
                    "info": "integer"
                },
                ...
                ...
        }
        ...
    ]

```

## Testing

```bash
./vendor/bin/phpunit
```

## Changelog

- Initial Release - POC
