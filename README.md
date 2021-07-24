# Laravel ERD Generator

Automatically generate interactive ERD from Models relationships in Laravel.

Details: https://medium.com/web-developer/laravel-automatically-generate-interactive-erd-from-eloquent-relations-83fe65440716

## Requirements

| Lang    | Version    |
| :------ | :--------- |
| PHP     | 7.4 or 8.0 |
| Laravel | 6.* or 8.* |

## Installation

You can install the package via composer:

```bash
composer require kevincobain2000/laravel-erd
```


You can publish the config file with:

```bash
php artisan vendor:publish --provider="Kevincobain2000\LaravelERD\LaravelERDServiceProvider"
```

## Usage

```php
php artisan erd:generate
```

ERD HTML is generated inside ``docs/``. Sample

![Image](https://i.imgur.com/tYk1CuC.png)

## Get JSON output

```php
use Kevincobain2000\LaravelERD\LaravelERD;

$namespace = 'App\Models\\';
$modelsPath = base_path('app/Models');
$linkDataArray = $this->laravelERD->getLinkDataArray($namespace, $modelsPath);
$nodeDataArray = $this->laravelERD->getNodeDataArray($namespace, $modelsPath);
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
