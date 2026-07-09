<?php

namespace Core;

use PDO;

class Database {

    private static $instance = null;

    private $conn;

    private $stmt;

    private function __construct() {
        $this->conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );

        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function query(string $query) {
        $this->stmt = $this->conn->prepare($query);
    }

    public function bind(string $param, $value) {
        $this->stmt->bindValue($param, $value);
    }

    public function execute(array $params = []) {
        return $this->stmt->execute($params);
    }

    public function resultSet() {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single() {
        $result = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollBack() {
        return $this->conn->rollBack();
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}