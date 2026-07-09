<?php

namespace Core;

use Exception;

class Transaction {

    public static function run(callable $callback) {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            $callback();

            $db->commit();

            return true;
        } catch (Exception $e) {
            $db->rollBack();

            error_log($e->getMessage());

            return false;
        }
    }
}