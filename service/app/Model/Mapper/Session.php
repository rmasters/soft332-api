<?php

namespace Model\Mapper;

class Session extends \Mapper
{
    public function save(\Model\Session $model) {
        $vars = array(
            ":user" => $model->user->id,
            ":token" => $model->token,
            ":created" => $model->created->format("Y-m-d H:i:s"),
            ":expires" => $model->expires->format("Y-m-d H:i:s")
        );

        $sql = "INSERT INTO session (user, token, created, expires) VALUES (:user, :token, :created, :expires)";

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($vars);
        $stmt->closeCursor();

        return $model;
    }

    public function find($token) {
        $sql = "SELECT * FROM session WHERE token = :token LIMIT 1";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute(array(":token" => $token));
        if ($stmt->rowCount() == 0) {
            $stmt->closeCursor();
            return false;
        }

        $model = new \Model\Session($stmt->fetch(\PDO::FETCH_ASSOC));
        $stmt->closeCursor();
        return $model;
    }

    public function delete(\Model\Session $model) {
        $stmt = self::getDb()->prepare("DELETE FROM session WHERE token = :token LIMIT 1");
        $stmt->execute(array(":token" => $model->token));
        $res = $stmt->rowCount() == 1;
        $stmt->closeCursor();
        return $res;
    }

    public function tokenExists($token) {
        $stmt = self::getDb()->prepare("SELECT COUNT(*) FROM session WHERE token = :token");
        $stmt->execute(array(":token" => $token));
        $count = $stmt->fetchColumn(0);
        $stmt->closeCursor();

        return $count > 0;
    }
}
