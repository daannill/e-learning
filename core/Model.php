<?php

namespace Core;

class Model {

    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    private function buildWhere(array $conditions, string $operator = 'AND'): array {
        $operator = strtoupper($operator);
 
        if (!in_array($operator, ['AND', 'OR'], true)) {
            throw new \InvalidArgumentException("Operator harus 'AND' atau 'OR', dapat: $operator");
        }
 
        $where = [];
        $params = [];
 
        foreach ($conditions as $column => $value) {
            $where[] = "$column = :$column";
            $params[":$column"] = $value;
        }
 
        return [implode(" $operator ", $where), $params];
    }

    protected function many(string $sql, array $params = []) {
        $this->db->query($sql);
        $this->db->execute($params);
        return $this->db->resultSet();
    }

    protected function one(string $sql, array $params = []) {
        $this->db->query($sql);
        $this->db->execute($params);
        return $this->db->single();
    }

    protected function run(string $sql, array $params = []) {
        $this->db->query($sql);
        return $this->db->execute($params);
    }

    protected function findByOne(string $table, array $columns, array $conditions, string $operator = 'AND'): ?array {
        [$where, $params] = $this->buildWhere($conditions, $operator);
        $columnList = implode(", ", $columns);
 
        return $this->one("
            SELECT
                $columnList
            FROM $table
            WHERE $where
            LIMIT 1
        ", $params);
    }

    protected function exists(string $table, array $conditions, string $operator = 'AND'): bool {
        return !empty($this->findByOne($table, ['1'], $conditions, $operator));
    }

    protected function findMany(string $table, array $columns = ['*'],  array $conditions = [], string $operator = 'AND'): array {
        $columnList = implode(', ', $columns);

        $sql = "
            SELECT $columnList
            FROM $table
        ";

        $params = [];

        if (!empty($conditions)) {
            [$where, $params] = $this->buildWhere($conditions, $operator);

            $sql .= "
                WHERE $where
            ";
        }

        return $this->many($sql, $params);
    }

    protected function insert(string $table, array $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $params = [];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }

        return $this->run("INSERT INTO `$table` ($columns) VALUES ($placeholders)", $params);
    }

    protected function update(
        string $table,
        array $data,
        array $conditions,
        string $operator = 'AND'
    ): bool {
        if (empty($conditions)) {
            throw new \InvalidArgumentException('Update tanpa kondisi WHERE tidak diperbolehkan.');
        }

        $set = [];
        $params = [];

        foreach ($data as $key => $value) {
            $set[] = "`$key` = :set_$key";
            $params[":set_$key"] = $value;
        }

        [$where, $whereParams] = $this->buildWhere($conditions, $operator);

        $params = array_merge($params, $whereParams);

        $sql = "
            UPDATE `$table`
            SET " . implode(', ', $set) . "
            WHERE $where
        ";

        return $this->run($sql, $params);
    }

    protected function delete(
        string $table,
        array $conditions,
        string $operator = 'AND'
    ): bool {

        if (empty($conditions)) {
            throw new \InvalidArgumentException(
                'Delete tanpa kondisi WHERE tidak diperbolehkan.'
            );
        }

        [$where, $params] = $this->buildWhere(
            $conditions,
            $operator
        );

        $sql = "
            DELETE
            FROM `$table`
            WHERE $where
        ";

        return $this->run($sql, $params);
    }

    protected function lastInsertId() {
        return $this->db->lastInsertId();
    }
}