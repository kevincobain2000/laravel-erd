<?php

namespace Kevincobain2000\LaravelERD;

use File;
use Schema;
use ReflectionClass;
use ReflectionMethod;
use Throwable;
use TypeError;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

class LaravelERD
{
    public function getModelsNames(string $namespace, string $modelsPath)
    {
        return collect(File::allFiles($modelsPath))
            ->map(function ($item) use ($namespace) {
                $path = $item->getRelativePathName();
                $class = sprintf(
                    '\%s%s',
                    $namespace,
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );
                return $class;
            })
            ->filter(function ($class) {
                $valid = false;

                if (class_exists($class)) {
                    $reflection = new ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) && !$reflection->isAbstract();
                }

                return $valid;
            });
    }

    public function getLinkDataArray(string $namespace, string $modelsPath)
    {
        $linkDataArray = [];
        $modelNames = $this->getModelsNames($namespace, $modelsPath);

        foreach ($modelNames as $modelName) {
            $model = app($modelName);
            $links = $this->getLinks($model);
            foreach ($links as $link) {
                $linkDataArray[] = $link;
            }
        }

        return $linkDataArray;
    }

    public function getNodeDataArray(string $namespace, string $modelsPath)
    {
        $nodeDataArray = [];
        $modelNames = $this->getModelsNames($namespace, $modelsPath);

        foreach ($modelNames as $modelName) {
            $model = app($modelName);
            $nodeDataArray[] = $this->getNodes($model);
        }
        return $nodeDataArray;
    }

    /**
     * Relationships
     *
     * @param Model $model
     * @return array of relationships
     */
    private function getRelationships(Model $model): array
    {
        $relationships = [];
        $model = new $model;

        foreach ((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class != get_class($model)
                || !empty($method->getParameters())
                || $method->getName() == __FUNCTION__
            ) {
                continue;
            }

            try {
                $return = $method->invoke($model);
                // check if not instance of Relation
                if (!($return instanceof Relation)) {
                    continue;
                }
                $relationType = (new ReflectionClass($return))->getShortName();
                $modelName = (new ReflectionClass($return->getRelated()))->getName();

                $foreignKey = $return->getQualifiedForeignKeyName();
                $parentKey = $return->getQualifiedParentKeyName();

                $relationships[$method->getName()] = [
                    'type'        => $relationType,
                    'model'       => $modelName,
                    'foreign_key' => $foreignKey,
                    'parent_key'  => $parentKey,
                ];
            } catch (QueryException $e) {
                // ignore
            } catch (TypeError $e) {
                // ignore
            } catch (Throwable $e) {
                // throw $e;
                //ignore
            }
        }

        return $relationships;
    }

    private function getNodes(Model $model): array
    {
        $nodeItems = [];
        $columns = Schema::getColumnListing($model->getTable());

        foreach ($columns as $column) {
            $keyName = $model->getKeyName();
            if (is_array($keyName)) {
                $isPrimaryKey = in_array($column, $keyName);
            } else {
                $isPrimaryKey = $column == $keyName;
            }

            $nodeItems[] = [
                "name"   => $column,
                "isKey"  => $isPrimaryKey,
                "figure" => $isPrimaryKey ? "Hexagon" : "Decision",
                "color"  => $isPrimaryKey ? "#be4b15" : "#6ea5f8",
                "info"   => Schema::getColumnType($model->getTable(), $column),
            ];
        }

        return [
            "key"    => $model->getTable(),
            "schema" => $nodeItems
        ];
    }

    private function getLinks(Model $model)
    {
        $relationships = $this->getRelationships($model);
        $linkItems = [];
        foreach ($relationships as $relationship) {
            $fromTable = $model->getTable();
            $toTable = app($relationship['model'])->getTable();

            // check if is array for multiple primary key
            if (is_array($relationship['foreign_key']) || is_array($relationship['parent_key'])) {
                // TODO add support for multiple primary keys
                $fromPort = ".";
                $toPort = ".";
            } else {
                $isBelongsTo = ($relationship['type'] == "BelongsTo" || $relationship['type'] == "BelongsToMany");
                $fromPort = $isBelongsTo ? $relationship["foreign_key"] : $relationship["parent_key"];
                $toPort   = $isBelongsTo ? $relationship["parent_key"] : $relationship["foreign_key"];
            }

            $linkItems[] = [
                "from"     => $fromTable,
                "to"       => $toTable,
                "fromText" => $this->getFromText($relationship),
                "toText"   => $this->getToText($relationship),
                "fromPort" => explode(".", $fromPort)[1], //strip tablename
                "toPort"   => explode(".", $toPort)[1],//strip tablename
                "type"     => $relationship['type'],
            ];
        }
        return $linkItems;
    }

    private function getFromText(array $relationship)
    {
        $text = '';
        switch ($relationship['type']) {
            case 'BelongsTo':
                $text = "1..1\nBT";
                break;
            case 'BelongsToMany':
                $text = "1..N\nBTM";
                break;
            case 'HasMany':
                $text = "1..N\nHM";
                break;
            case 'HasOne':
                $text = "1..1\nHO";
                break;
            case 'MorphTo':
                $text = "1..1\nMT";
                break;
            case 'MorphMany':
                $text = "1..N\nMM";
                break;
        }
        return $text;
    }

    private function getToText(array $relationship)
    {
        $text = '';
        switch ($relationship['type']) {
            case 'BelongsTo':
                $text = '';
                break;
            case 'HasMany':
                $text = '';
                break;
            case 'HasOne':
                $text = '';
                break;
        }
        return $text;
    }
}
