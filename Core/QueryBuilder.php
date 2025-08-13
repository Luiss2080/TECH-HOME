<?php

namespace Core;

use PDO;
use PDOException;

class QueryBuilder
{
    private $db;
    private $table;
    private $query;
    private $bindings = [];
    private $modelClass = null;
    private $eagerLoads = [];
    public function __construct(DB $db, string $table, string $modelClass = '')
    {
        $this->db = $db;
        $this->table = $table;
        $this->query = new \stdClass();
        if (!empty($modelClass)) {
            $this->modelClass = $modelClass;
        }
    }

    public function select(array $columns = ['*'])
    {
        $this->query->type = 'select';
        $this->query->select = implode(', ', $columns);
        return $this;
    }

    public function where(string $column, string $operator, $value)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        if (!is_string($operator)) {
            throw new QueryException('Invalid operator', $this->query);
        }
        $this->query->where[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function whereRaw(string $sql, array $bindings = [])
    {
        $this->query->where[] = $sql;
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    public function count(): int
    {
        $originalSelect = $this->query->select ?? '*';
        $this->query->select = 'COUNT(*) as count';
        $result = $this->get();

        // Restore original select
        $this->query->select = $originalSelect;

        return (int) ($result[0]->count ?? 0);
    }

    public function get()
    {
        if (!isset($this->query->select)) {
            $this->query->select = '*';
        }
        $sql = "SELECT {$this->query->select} FROM {$this->table}";
        if (!empty($this->query->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->query->where);
        }
        if (!empty($this->query->orderBy)) {
            $sql .= " {$this->query->orderBy}";
        }
        if (!empty($this->query->limit)) {
            $sql .= " LIMIT {$this->query->limit}";
        }
        $results = $this->db->query($sql, $this->bindings)->fetchAll();
        // verificar que los resultados no estén vacíos
        // verificar que la consulta no este Count,Avg porque estamos mapeando a modelos
        if ($this->modelClass && !empty($results) && !preg_match('/\b(COUNT|AVG)\b/', $sql)) {
            $results = array_map(function ($result) {
                $model = new $this->modelClass;
                $model->fill((array) $result);
                return $model;
            }, $results);

            if (!empty($this->eagerLoads)) {
                foreach ($results as $model) {
                    $this->loadRelations($model);
                }
            }
        }
        return $results;
    }
    private function loadRelations(Model $model)
    {
        foreach ($this->eagerLoads as $relation) {
            if (method_exists($model, $relation)) {
                $this->loadRelation($model, $relation);
            }
        }
    }
    private function loadRelation(Model $model, $relation)
    {
        $relationMethod = $relation;
        $relationResult = $model->$relationMethod();

        if ($relationResult instanceof Model) {
            // Relación belongsTo
            $model->setRelation($relation, $relationResult);
        } elseif (is_array($relationResult)) {
            // Relación hasMany/hasOne
            $model->setRelation($relation, $relationResult);
        }
    }

    public function first()
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }
    private function getSqlDebug()
    {
        $select = $this->query->select ?? '*';
        $sql = "SELECT {$select} FROM {$this->table}";
        if (!empty($this->query->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->query->where);
        }
        if (!empty($this->query->orderBy)) {
            $sql .= " {$this->query->orderBy}";
        }
        if (!empty($this->query->limit)) {
            $sql .= " LIMIT {$this->query->limit}";
        }
        foreach ($this->bindings as $binding) {
            $sql = preg_replace('/\?/', "'{$binding}'", $sql, 1);
        }
        return $sql;
    }
    public function limit(int $limit)
    {
        $this->query->limit = $limit;
        return $this;
    }
    public function orderBy(string $column, string $direction = 'ASC')
    {
        $this->query->orderBy = "ORDER BY {$column} {$direction}";
        return $this;
    }
    public function with(array $relationships)
    {
        $this->eagerLoads = $relationships;
        return $this;
    }
    public function whereBetween(string $column, array $value1)
    {
        $this->query->where[] = "{$column} BETWEEN ? AND ?";
        $this->bindings[] = $value1[0];
        $this->bindings[] = $value1[1] ?? $value1[0];
        return $this;
    }
    public function whereIn(string $column, array $values)
    {
        $this->query->where[] = "{$column} IN (" . implode(', ', array_fill(0, count($values), '?')) . ")";
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }
    public function getBindings()
    {
        return $this->bindings;
    }
}
class QueryException extends \RuntimeException
{
    public $sql;

    public function __construct($message, $sql)
    {
        $this->sql = $sql;
        parent::__construct($message);
    }
}
