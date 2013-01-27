<?php

namespace Resource\A;

class User extends Resource
{
    public function GET($id) {
        $mapper = new \Model\Mapper\User;

        $model = $mapper->findByName($id);
        if (!$model) {
            throw new \Error\NotFound("No user found with that username");
        }

        $data = $model->toArray();
        return $data;
    }

    public function POST($id = null) {
        $mapper = new \Model\Mapper\User;
        if (!is_null($id)) {
            $model = $mapper->findByName($id);

            if (!isset($this->request->session->user) ||
                $this->request->session->user->id != $model->id) {
                throw new \Error\NotAuthorised("You must be authenticated as the user you wish to edit");
            }
        } else {
            if (isset($this->request->session->user)) {
                throw new \Error\NotAuthorised("You cannot be authenticated and create another user");
            }

            $model = new \Model\User;

            if (!isset($_POST["name"], $_POST["password"])) {
                throw new \Error\MissingParameters("Supply a username and password");
            }
        }

        if (isset($_POST["name"])) {
            $model->name = $_POST["name"];
        }

        if (isset($_POST["password"])) {
            $model->password = \Model\User::hashPassword($_POST["password"]);
        }

        $mapper->save($model);

        if (is_null($id)) {
            return array("message" => "Created user", "user" => $model->toArray());
        } else {
            return array("message" => "Updated user", "user" => $model->toArray());
        }
    }

    public function DELETE($id) {
        if (!isset($this->request->session->user) ||
            $this->request->session->user->id != $model->id) {
            throw new \Error\NotAuthorised;
        }

        $mapper = new \Model\Mapper\User;

        $model = $mapper->findByName($id);
        if (!$model) {
            throw new \Error\NotFound("User not found");
        }
        $username = $model->name;

        $result = $mapper->delete($model);
        if ($result) {
            return array("message" => "User " . $username . " deleted.");
        } else {
            return array("message" => "Failed to delete user " . $username);
        }
    }
}
