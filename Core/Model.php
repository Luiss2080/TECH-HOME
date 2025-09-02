<?php

namespace Core;


class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;
    protected $softDeletes = false;

    private $attributes = [];
    private $original = [];
    private $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);

        if (!$this->table) {
            $this->table = strtolower(class_basename($this)) . 's';
        }
    }

    public function fill(array $attributes)
    {
        $isHydrating = isset($attributes[$this->primaryKey]);

        if ($isHydrating) {
            foreach ($attributes as $key => $value) {
                $this->attributes[$key] = $value;
                $this->$key = $value;
            }
            $this->exists = true;
        } else {
            foreach ($attributes as $key => $value) {
                if (in_array($key, $this->fillable)) {
                    $this->attributes[$key] = $value;
                    $this->$key = $value;
                }
            }
        }

        if ($this->timestamps && !$this->exists) {
            $createdAt = method_exists($this, 'getCreatedAtColumn') ? $this->getCreatedAtColumn() : 'created_at';
            $updatedAt = method_exists($this, 'getUpdatedAtColumn') ? $this->getUpdatedAtColumn() : 'updated_at';
            
            $this->attributes[$createdAt] = $this->attributes[$createdAt] ?? date('Y-m-d H:i:s');
            $this->attributes[$updatedAt] = $this->attributes[$updatedAt] ?? date('Y-m-d H:i:s');
        }

        return $this;
    }

    public function save()
    {
        $db = DB::getInstance();
        if ($this->exists) {
            return $this->performUpdate($db);
        }

        return $this->performInsert($db);
    }

    protected function performInsert(DB $db)
    {
        $attributes = $this->getAttributesForSave();

        if ($this->timestamps) {
            $createdAt = method_exists($this, 'getCreatedAtColumn') ? $this->getCreatedAtColumn() : 'created_at';
            $updatedAt = method_exists($this, 'getUpdatedAtColumn') ? $this->getUpdatedAtColumn() : 'updated_at';
            
            $attributes[$createdAt] = date('Y-m-d H:i:s');
            $attributes[$updatedAt] = date('Y-m-d H:i:s');
        }

        $columns = implode(', ', array_keys($attributes));
        $placeholders = implode(', ', array_fill(0, count($attributes), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $db->query($sql, array_values($attributes));

        $this->attributes[$this->primaryKey] = $db->getConnection()->lastInsertId();
        $this->exists = true;

        return $this;
    }

    protected function performUpdate(DB $db)
    {
        $attributes = $this->getAttributesForSave();

        if ($this->timestamps) {
            $updatedAt = method_exists($this, 'getUpdatedAtColumn') ? $this->getUpdatedAtColumn() : 'updated_at';
            $attributes[$updatedAt] = date('Y-m-d H:i:s');
        }

        $updates = [];
        foreach ($attributes as $key => $value) {
            $updates[] = "{$key} = ?";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) .
            " WHERE {$this->primaryKey} = ?";

        $bindings = array_values($attributes);
        $bindings[] = $this->getKey();

        $db->query($sql, $bindings);

        return $this;
    }

    public function delete()
    {
        if ($this->softDeletes) {
            return $this->performSoftDelete();
        }
        return $this->performForceDelete();
    }

    protected function performForceDelete()
    {
        $db = DB::getInstance();
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $db->query($sql, [$this->getKey()]);
        $this->exists = false;
        return true;
    }
    public function getKey()
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }

    protected function performSoftDelete()
    {
        $this->attributes['deleted_at'] = date('Y-m-d H:i:s');
        return $this->save();
    }

    protected function newQuery()
    {
        return DB::getInstance()->table($this->table, static::class);
    }

    /**
     * Obtener nombre de columna para created_at (puede ser sobrescrito en modelos hijos)
     */
    protected function getCreatedAtColumn()
    {
        return 'created_at';
    }

    /**
     * Obtener nombre de columna para updated_at (puede ser sobrescrito en modelos hijos)
     */
    protected function getUpdatedAtColumn()
    {
        return 'updated_at';
    }
    protected function getAttributesForSave()
    {
        $attributes = [];

        // Solo incluir campos fillable
        foreach ($this->fillable as $field) {
            if (array_key_exists($field, $this->attributes)) {
                $attributes[$field] = $this->attributes[$field];
            }
        }

        // Manejo de timestamps
        if ($this->timestamps) {
            if (!$this->exists) {
                $attributes['created_at'] = $this->attributes['created_at'] ?? date('Y-m-d H:i:s');
            }
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }

        // Manejo de soft deletes
        if ($this->softDeletes && isset($this->attributes['deleted_at'])) {
            $attributes['deleted_at'] = $this->attributes['deleted_at'];
        }

        return $attributes;
    }
    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        if ($this->timestamps && in_array($name, ['created_at', 'updated_at'])) {
            return $this->attributes[$name] ?? null;
        }
        if (method_exists($this, $name)) {
            $result = $this->$name();
            $this->attributes[$name] = $result;
            return $result;
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->fillable)) {
            $this->attributes[$name] = $value;
        }
    }
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getFillable()
    {
        return $this->fillable;
    }
    public static function query()
    {
        $instance = new static();
        return DB::getInstance()->table($instance->table, static::class);
    }

    public static function where($column, $operator, $value = null)
    {
        if (!static::isOperator($operator) && !isset($value)) {
            $value = $operator;
            $operator = '=';
        } else if (!isset($value) && static::isOperator($operator)) {
            throw new \Core\QueryException('Operador no válido', $operator);
        }
        return static::query()->where($column, $operator, $value);
    }
    protected static function isOperator($operator): bool
    {
        $operators = [
            '=',
            '<',
            '>',
            '<=',
            '>=',
            '<>',
            '!=',
            'like',
            'not like',
            'between',
            'ilike',
            '&',
            '|',
            '^',
            '<<',
            '>>',
            'rlike',
            'regexp',
            'not regexp',
            '~',
            '~*',
            '!~',
            '!~*',
            'similar to',
            'not similar to'
        ];
        return in_array(strtolower($operator), $operators);
    }

    public static function find($id)
    {
        return static::query()->where((new static())->primaryKey, '=', $id)->first();
    }

    public static function all()
    {
        return static::query()->get();
    }

    public static function whereIn($column, array $values)
    {
        return static::query()->whereIn($column, $values);
    }

    public static function whereRaw(string $sql, array $bindings = [])
    {
        return static::query()->whereRaw($sql, $bindings);
    }

    public static function count()
    {
        return static::query()->count();
    }

    public static function orderBy(string $column, string $direction = 'ASC')
    {
        return static::query()->orderBy($column, $direction);
    }

    public static function limit(int $limit)
    {
        return static::query()->limit($limit);
    }
    // relaciones 

    public function belongsTo($related, $foreignKey = null, $ownerKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $ownerKey = $ownerKey ?: 'id';

        $relatedModel = new $related;
        return $relatedModel->find($this->{$foreignKey});
    }
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: 'id';

        $relatedModel = new $related;
        return $relatedModel->where($foreignKey, '=', $this->{$localKey})->first();
    }
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: 'id';
        $relatedModel = new $related;
        return $relatedModel->where($foreignKey, '=', $this->{$localKey})->get();
    }
    protected function getForeignKey()
    {
        $relatedClass = get_class($this);
        $relatedClassName = class_basename($relatedClass);
        return strtolower($relatedClassName) . 'Id';
    }
    public function setRelation($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getRelation($name)
    {
        return $this->attributes[$name] ?? null;
    }
    public static function with(array $relations): QueryBuilder
    {
        $instance = new static();
        return DB::getInstance()->table($instance->table, static::class)->with($relations);
    }
}
