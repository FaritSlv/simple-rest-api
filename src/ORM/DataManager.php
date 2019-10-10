<?php

namespace App\ORM;


class DataManager {

    /** @var DB */
    private $db = null;

    /** @var string */
    private $tableName = null;

    /**
     * DataManager constructor.
     * @param string $tableName
     */
    public function __construct($tableName = null) {
        $this->db        = new DB();
        $this->tableName = $tableName;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data) {
        $columns = $this->getColumns($data);
        $values  = $this->getValues($data);
        $sql     = "INSERT INTO \"{$this->tableName}\" ({$columns}) VALUES ({$values})";
        return $this->db->query($sql)->execute();
    }

    /**
     * @param array $select
     * @return array
     */
    public function getList(array $select = []) {
        $selectQuery = count($select) > 0 ? $this->getSelect($select) : "*";
        $sql         = "SELECT {$selectQuery} from \"{$this->tableName}\"";
        return $this->db->query($sql)->result();
    }

    /**
     * @param $primary
     * @param array $data
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function update($primary, array $data, $key = null) {
        if ((int)$primary === 0) {
            throw new Exception("Primary is required field");
        }

        $primaryKey = $key !== null ? (string)$key : "id";
        $updateData = [];
        foreach ($data as $keyData => $valueData) {
            $updateData[] = "\"{$keyData}\" = " . $this->db->quote($valueData);
        }

        $updateStr = implode(",", $updateData);
        $sql = "UPDATE \"{$this->tableName}\" SET {$updateStr} WHERE \"{$primaryKey}\" = {$primary}";
        return $this->db->query($sql)->execute();
    }

    /**
     * @param $primary
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function delete($primary, $key = null) {
        if ((int)$primary === 0) {
            throw new Exception("Primary is required field");
        }
        $primaryKey = $key !== null ? (string)$key : "id";
        $sql        = "DELETE FROM \"{$this->tableName}\" WHERE \"{$primaryKey}\" = {$primary}";

        return $this->db->query($sql)->execute();
    }

    /**
     * @param array $select
     * @return string
     */
    private function getSelect(array $select = []) {
        $result = [];
        foreach ($select as $key => $column) {
            if (is_numeric($key)) {
                $result[] = "\"{$this->tableName}\".\"{$column}\"";
            } else {
                $result[] = "\"{$this->tableName}\".\"{$column}\" AS " . $this->db->quote($key);
            }
        }
        return implode(",", $result);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getColumns(array $data = []) {
        $columns = array_keys($data);
        $result  = array_map(
            function($item) {
                return '"' . $item . '"';
            },
            $columns
        );

        return implode(",", $result);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getValues(array $data = []) {
        $columns = array_values($data);
        $result  = array_map(
            function($item) {
                return $this->db->quote($item);
            },
            $columns
        );

        return implode(",", $result);
    }
}