<?php
// app/core/Model.php

class Model {
    protected static $table = '';

    protected static function getDB() {
        return DB::getInstance();
    }

    public static function find($id) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM ".static::$table." WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject(get_called_class());
    }

    public static function findByTg($id) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM ".static::$table." WHERE telegram_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject(get_called_class());
    }

    public static function all() {
        $db = self::getDB();
        $stmt = $db->query("SELECT * FROM ".static::$table);
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public function save() {
        $db = self::getDB();
        $properties = get_object_vars($this);

        if (isset($this->id)) {
            // Update
            $set = [];
            foreach ($properties as $key => $value) {
                if ($key === 'id') continue;
                $set[] = "$key = :$key";
            }
            $stmt = $db->prepare("UPDATE ".static::$table." SET ".implode(', ', $set)." WHERE id = :id");
        } else {
            // Insert
            $columns = implode(', ', array_keys($properties));
            $values = ':'.implode(', :', array_keys($properties));
            $stmt = $db->prepare("INSERT INTO ".static::$table." ($columns) VALUES ($values)");
        }

        return $stmt->execute($properties);
    }

    public function delete() {
        if (!isset($this->id)) return false;

        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM ".static::$table." WHERE id = ?");
        return $stmt->execute([$this->id]);
    }
}