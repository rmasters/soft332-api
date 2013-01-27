<?php

class NotAuthenticatedException extends Exception {}
class APIException extends Exception {}

class Mumblr
{
    const API_VERSION = 1;

    const GET = 1;
    const POST = 2;
    const PUT = 3;
    const DELETE = 4;
    const HEAD = 5;

    public static $baseURL = "http://api.soft332.mario.rossmasters.com";
    private $authToken;
    public $userInfo;

    public function __construct() {
        if (isset($_SESSION["mumblr_token"])) {
            $this->authToken = $_SESSION["mumblr_token"];
        }
        if (isset($_SESSION["user"])) {
            $this->userInfo = unserialize($_SESSION["user"]);
        }
    }
    
    public function __destruct() {
        $_SESSION["mumblr_token"] = $this->authToken;
        $_SESSION["user"] = serialize($this->userInfo);
    }

    public function get_user($id) {
        return $this->request("/user/" . $id);
    }

    public function new_user(array $params) {
        return $this->request("/user", self::POST, $params);
    }

    public function delete_user($id) {
        return $this->request("/user/" . $id, self::DELETE);
    }

    public function save_user($id, array $params) {
        return $this->request("/user/" . $id, self::POST, $params);
    }

    public function login($username, $password) {
        $result = $this->request("/session", self::POST,
            array("username" => $username, "password" => $password));

        if (isset($result->session)) {
            $this->authToken = $result->session->token;
            $this->userInfo = $result->session->user;
        }

        return $result;
    }

    public function session_status() {
        return $this->request("/session");
    }

    public function logout() {
        if (!isset($this->authToken)) {
            throw new NotAuthenticatedException;
        }
        $result = $this->request("/session", self::DELETE);

        $this->authToken = null;
        $this->userInfo = null;

        return $result;
    }

    public function new_post($message) {
        if (!isset($this->authToken)) {
            throw new NotAuthenticatedException;
        }

        $result = $this->request("/post", self::POST, array("message" => $message));
        return $result;
    }

    public function get_post($id) {
        return $this->request("/post/" . $id);
    }

    public function delete_post($id) {
        if (!isset($this->authToken)) {
            throw new NotAuthenticatedException;
        }
        return $this->request("/post/" . $id, self::DELETE);
    }

    public function get_stream() {
        return $this->request("/stream");
    }

    public function authenticated() {
        return isset($this->authToken);
    }

    public function set_token($token) {
        $this->authToken = $token;
    }

    protected function request($uri, $method = self::GET, array $postData = null) {
        $url = self::$baseURL . "/" . self::API_VERSION . "/" . trim($uri, "/");

        $handle = curl_init();

        // Return the result, rather than writing it to ob
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);

        // Additional HTTP headers to be sent
        $headers = array(
            "Accept: application/json"
        );

        // Send the authentication token if one is set
        if (isset($this->authToken)) {
            $headers[] = "X-Auth-Token: " . $this->authToken;
        }

        // Add additional headers to the request
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);

        switch ($method) {
        case self::GET:
            break;
        case self::POST:
            // Attach post data
            if (is_array($postData)) {
                curl_setopt($handle, CURLOPT_POSTFIELDS, $postData);
            }
            break;
        case self::PUT:
            // Use a PUT request
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
            // Attach post data
            if (is_array($postData)) {
                curl_setopt($handle, CURLOPT_POSTFIELDS, $postData);
            }
            break;
        case self::DELETE:
            // Use a DELETE request
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
        default:
            throw new APIException;
        }

        $result = curl_exec($handle);
        curl_close($handle);

        return json_decode($result);
    }

}
