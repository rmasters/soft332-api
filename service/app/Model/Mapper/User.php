<?php

namespace Model\Mapper;

class User extends \Mapper
{
    public function save(\Model\User $model) {
        $vars = array(
            ":name" => $model->name,
            ":registered" => $model->registered->format("Y-m-d H:i:s"),
            ":password" => $model->password,
            ":id" => $model->id
        );

        if (isset($model->id)) {
            unset($vars[":registered"]);
            $sql = "UPDATE user SET name = :name, password = :password WHERE id = :id";
        } else {
            unset($vars[":id"]);
            $sql = "INSERT INTO user SET name = :name, password = :password, registered = :registered";
        }

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($vars);

        if (!isset($model->id)) {
            $model->id = self::getDb()->lastInsertId();
        }

        $stmt->closeCursor();

        return $model;
    }

    public function find($id) {
        $sql = "SELECT * FROM user WHERE id = :id LIMIT 1";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute(array(":id" => $id));
        if ($stmt->rowCount() == 0) {
            $stmt->closeCursor();
            return false;
        }

        $model = new \Model\User($stmt->fetch(\PDO::FETCH_ASSOC));
        $stmt->closeCursor();
        return $model;
    }

    public function findByName($name) {
        $sql = "SELECT * FROM user WHERE name = LOWER(:name) LIMIT 1";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute(array(":name" => $name));
        if ($stmt->rowCount() == 0) {
            $stmt->closeCursor();
            return false;
        }

        $model = new \Model\User($stmt->fetch(\PDO::FETCH_ASSOC));
        $stmt->closeCursor();
        return $model;
    }

    public function findByCredentials($username, $password) {
        $password = \Model\User::hashPassword($password);

        $sql = "SELECT * FROM user WHERE name = LOWER(:username) AND password = :password LIMIT 1";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute(array(":username" => $username, ":password" => $password));
        if ($stmt->rowCount() == 0) {
            $stmt->closeCursor();
            return false;
        }

        $model = new \Model\User($stmt->fetch(\PDO::FETCH_ASSOC));
        $stmt->closeCursor();
        return $model;
    }

    public function delete(\Model\User $model) {
        $stmt = self::getDb()->prepare("DELETE FROM user WHERE id = :id LIMIT 1");
        $stmt->execute(array(":id" => $model->id));
        $res = $stmt->rowCount() == 1;
        $stmt->closeCursor();
        return $res;
    }
}
