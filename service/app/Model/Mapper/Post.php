<?php

namespace Model\Mapper;

class Post extends \Mapper
{
    public function save(\Model\Post $model) {
        $vars = array(
            ":user" => isset($model->user) ? $model->user->id : null,
            ":message" => $model->message,
            ":posted" => $model->posted->format("Y-m-d H:i:s"),
            ":id" => $model->id
        );

        if (isset($model->id)) {
            unset($vars[":posted"]);
            $sql = "UPDATE post SET user = :user, message = :message WHERE id = :id";
        } else {
            unset($vars[":id"]);
            $sql = "INSERT INTO post (user, message, posted) VALUES (:user, :message, :posted)";
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
        $sql = "SELECT * FROM post WHERE id = :id LIMIT 1";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute(array(":id" => $id));
        if ($stmt->rowCount() == 0) {
            $stmt->closeCursor();
            return false;
        }

        $model = new \Model\Post($stmt->fetch(\PDO::FETCH_ASSOC));
        $stmt->closeCursor();
        return $model;
    }

    public function fetchAllByPosted($direction = "desc", $count = 30, $offset = 0) {
        $direction = strtoupper($direction);
        $direction = $direction == "ASC" ? "ASC" : "DESC";

        $sql = "SELECT * FROM post ORDER BY posted " . $direction . " LIMIT " . $offset . ", " . $count;
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute(array(":count" => $count, ":offset" => $offset));

        $posts = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $posts[] = new \Model\Post($row);
        }
        $stmt->closeCursor();

        return $posts;
    }

    public function delete(\Model\Post $model) {
        $stmt = self::getDb()->prepare("DELETE FROM post WHERE id = :id LIMIT 1");
        $stmt->execute(array(":id" => $model->id));
        $res = $stmt->rowCount() == 1;
        $stmt->closeCursor();
        return $res;
    }
}
