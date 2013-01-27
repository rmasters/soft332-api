<?php
/**
 * The mapper is an interface between the Model and the database
 *
 * In this instance we are just using it as a container for our
 * database connection.
 */
class Mapper
{
    private static $db;

    public static function setDb(\PDO $db) {
        self::$db = $db;
    }

    public static function getDb() {
        return self::$db;
    }
}
