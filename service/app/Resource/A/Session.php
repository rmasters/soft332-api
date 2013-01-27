<?php

namespace Resource\A;

class Session extends Resource
{
    public function GET($token = null) {
        $session = false;
        if (!is_null($token)) {
            $mapper = new \Model\Mapper\Session;
            $session = $mapper->find($token);

            if (!$session) {
                throw new \Error\NotFound("Invalid session token");
            }
        } else if (isset($this->request->session)) {
            if (!$this->request->session) {
                return array("status" => "Session expired");
            }
            $session = $this->request->session;
        }

        if (!$session) {
            throw new \Error\MissingParameters("Missing a session token");
        }

        if ($session->expired()) {
            return array("status" => "Session expired");
        }

        return array_merge(array("status" => "Session valid"), $session->toArray());

    }

    public function POST() {
        if (isset($this->request->user)) {
            throw new \Error\NotAuthorised("You are already authenticated, make this request without a X-Auth-Token header");
        }

        // Authenticate the user
        if (!isset($_POST["username"], $_POST["password"])) {
            throw new \Error\MissingParameters("username and password must be supplied");
        }

        $mapper = new \Model\Mapper\User;
        $user = $mapper->findByCredentials($_POST["username"], $_POST["password"]);
        if (!$user) {
            throw new \Error\InvalidCredentials("Credentials were invalid");
        }

        $sessionMapper = new \Model\Mapper\Session;
        $session = new \Model\Session;
        $session->user = $user;

        // Find and generate a token that has not been used yet
        while (!isset($session->token)) {
            $token = \Model\Session::generateToken();
            if (!$sessionMapper->tokenExists($token)) {
                $session->token = $token;
            }
        }

        $sessionMapper->save($session);

        return array("message" => "Authenticated as " . $session->user->name, "session" => $session->toArray());
    }

    /**
     * Mark the current session as ended, preventing a token from being used later on
     */
    public function DELETE($token = null) {
        if (isset($token)) {
            $model = new \Model\Session(array("token" => $token));
        } else if (isset($this->request->session)) {
            $model = $this->request->session;
        } else {
            throw new \Error\MissingParameters("Missing session token");
        }

        $mapper = new \Model\Mapper\Session;
        $rows = $mapper->delete($model);

        if ($rows == 1) {
            return array("status" => "Session ended.");
        } else {
            return array("status" => "Session not ended, probably already closed.");
        }
    }
}
