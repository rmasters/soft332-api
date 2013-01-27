<?php

namespace Resource\A;

class Post extends Resource
{
    public function GET($id) {
        $mapper = new \Model\Mapper\Post;
        $model = $mapper->find($id);
        if (!$model) {
            throw new \Error\NotFound("Post not found");
        }

        $data = $model->toArray();
        return $data;
    }

    public function POST() {
        if (!isset($this->request->session->user)) {
            throw new \Error\NotAuthorised;
        }

        $mapper = new \Model\Mapper\Post;
        $model = new \Model\Post;
        $model->user = $this->request->session->user;

        if (!isset($_POST["message"])) {
            throw new \Error\MissingParameters("Missing message parameter");
        }

        $model->message = $_POST["message"];

        $mapper->save($model);

        return array("message" => "Posted successfully", "post" => $model->toArray());
    }

    public function DELETE($id) {
        $mapper = new \Model\Mapper\Post;
        $model = $mapper->find($id);
        if (!$model) {
            throw new \Error\NotFound("Post not found");
        }

        if (!isset($this->request->session->user) ||
            $this->request->session->user->id != $model->user->id) {
            throw new \Error\NotAuthorised;
        }

        $result = $mapper->delete($model);
        if ($result) {
            return array("message" => "Post deleted");
        } else {
            return array("message" => "Failed to delete post");
        }
    }
}
