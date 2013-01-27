<?php
/**
 * Single-entry point for all API requests
 */

require __DIR__ . "/../app/bootstrap.php";

try {
    // Setup a router, that matches URIs and HTTP verbs to resources
    $router = new Router;

    $router->setRoutes(array(
        // Authenticate a user
        array("match" => "/:version/session", "verb" => "POST", "resource" => "Session"),
        // Check the current session status
        array("match" => "/:version/session", "verb" => "GET", "resource" => "Session"),
        array("match" => "/:version/session/:token", "verb" => "GET", "resource" => "Session"),
        // Finish a session
        array("match" => "/:version/session", "verb" => "DELETE", "resource" => "Session"),
        array("match" => "/:version/session/:token", "verb" => "DELETE", "resource" => "Session"),

        // Get a user profile
        array("match" => "/:version/user/:id", "verb" => "GET", "resource" => "User"),
        // Register a new user
        array("match" => "/:version/user", "verb" => "POST", "resource" => "User"),
        // Modify a user's information
        array("match" => "/:version/user/:id", "verb" => "POST", "resource" => "User"),
        // Delete a user
        array("match" => "/:version/user/:id", "verb" => "DELETE", "resource" => "User"),
        
        // Create a new post
        array("match" => "/:version/post", "verb" => "POST", "resource" => "Post"),
        // Get a post
        array("match" => "/:version/post/:id", "verb" => "GET", "resource" => "Post"),
        // Delete a post
        array("match" => "/:version/post/:id", "verb" => "DELETE", "resource" => "Post"),

        // Get a stream of posts
        array("match" => "/:version/stream", "verb" => "GET", "resource" => "Stream"),
    ));

    // Find a match based on the current HTTP action and URI
    $request = $router->route($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);

    // If there was no match, return a 404
    if (!$request) {
        throw new Error\NotFound("No resource found for your request");
    }

    // Request::$params provides a match for all the :variables in the URI that have been matched
    // If version is not set, assume the client wants the latest one
    if (isset($request->params["version"])) {
        $request->version = $request->params["version"];
        unset($request->params["version"]);
    } else {
        $request->version = LATEST_VERSION;
    }

    // Check for an authentication token
    if (isset($_SERVER["HTTP_X_AUTH_TOKEN"])) {
        // Load the Session model to see if the token exists in the database
        $sessionMapper = new Model\Mapper\Session;
        $session = $sessionMapper->find($_SERVER["HTTP_X_AUTH_TOKEN"]);
        // If the token's valid, use it
        if ($session && !$session->expired()) {
            $request->session = $session;
        } else {
            // We check this in the Session resource
            $request->session = false;
        }
    }

    // Versions of our API are namespaced in Request\VERSION, however PHP does
    // not allow identifiers to start with numbers, so we convert them to letters
    $alpha = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J"); // 10, for now
    // Build the class name of the REST resource
    $class = sprintf('Resource\%s\%s', $alpha[$request->version-1], $request->resource);

    // Instantiate the resource (controller)
    $resource = new $class;
    // Store the Request (some of the HTTP request data)
    $resource->setRequest($request);
    // Check if an action for the HTTP verb exists (i.e. GET(), POST())
    if (!method_exists($resource, $request->verb)) {
        throw new Error\NotFound("No use for {$request->verb} on $resource");
    }

    // Execute the HTTP method on the REST resource, passing in matched parameters from the URL
    $response = call_user_func_array(array($resource, $request->verb), $request->params);

    // We are returning JSON, so encode it
    header("Content-type: application/json");
    echo json_encode($response);
} catch (Exception $e) {
    // Last-chance error reporting
    $code = $e->getCode() > 0 ? $e->getCode() : 500;
    header("Content-type: application/json", null, $code);
    echo json_encode(array("error" => $e->getMessage(), "code" => $code));
}
