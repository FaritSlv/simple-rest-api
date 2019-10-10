<?php

namespace App\ORM;

use PDO;
use PDOStatement;

class DB {

    /** @var PDO */
    private $db = null;
    /** @var PDOStatement */
    private $stmt = null;

    public function __construct() {
        $dbFile   = DB_PATH;
        $this->db = new PDO(
            "sqlite:$dbFile",
            null,
            null,
            [PDO::ATTR_PERSISTENT => true]
        );
    }

    /**
     * @param $query
     * @return $this
     */
    public function query($query) {
        $this->stmt = $this->db->prepare($query);
        return $this;
    }

    /**
     * @param $pos
     * @param $value
     * @param null $type
     * @return $this
     */
    public function bind($pos, $value, $type = null) {
        $typeSet = $type !== null ? $type : $this->getType($value);
        $this->stmt->bindValue($pos, $value, $typeSet);
        return $this;
    }

    /**
     * @param $value
     * @param null $type
     * @return string
     */
    public function quote($value, $type = null) {
        $typeSet = $type !== null ? $type : $this->getType($value);
        return $this->db->quote($value, $typeSet);
    }

    /**
     * @param $value
     * @return int
     */
    public function getType($value) {
        switch (true) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * @return bool
     */
    public function execute() {
        return $this->stmt->execute();
    }

    /**
     * @return array
     */
    public function result() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
}