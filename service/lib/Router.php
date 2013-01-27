<?php
/**
 * Matches a URL string to an array of 'routes'
 *
 * We hold a list of URI and HTTP Verb pairs which we can match against later
 * on to detect the requested REST resource.
 *
 * Fairly similar to Zend Framework's router.
 */
class Router
{
    protected $routes = array();

    public function addRoute(array $route) {
        if (isset($route["match"], $route["verb"], $route["resource"])) {
            // Create a regular expression pattern to match against the URI
            $route["params"] = array();
            $pattern = $route["match"];

            // Match variables like :id or :username and record them so we can name them later
            if (preg_match_all("/:([A-Za-z0-9_]+)/", $route["match"], $params, PREG_SET_ORDER) > 0) {
                foreach ($params as $match) {
                    $route["params"][$match[0]] = $match[1];
                }

                // Replace the instances of :variable in the URI with very loose regex captures
                // [^!/]+ captures anything that isn't a forward slash.
                $pattern = str_replace(array_keys($route["params"]), "([^!/]+?)", $route["match"]);
            }

            // Escape special characters in the regex
            $pattern = str_replace("/", "\/", $pattern);
            // Add start and end markers to the regex
            $route["pattern"] = '/^' . $pattern . '$/';

            $this->routes[] = $route;
        } else {
            throw new Exception("Route must contain a URI match, HTTP verb and Resource");
        }
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function addRoutes(array $routes) {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    public function setRoutes(array $routes) {
        $this->routes = array();
        $this->addRoutes($routes);
    }

    /**
     * Attempt to find a match of a URI and verb
     */
    public function route($uri, $verb) {
        foreach ($this->routes as $route) {
            if ($verb == $route["verb"]) {
                // If the verb matches, try to run the regex against the URI
                if (preg_match($route["pattern"], $uri, $matches) === 1) {
                    $request = new Request;
                    $request->verb = $route["verb"];
                    $request->resource = $route["resource"];

                    // If there were any parameters in the regex, we can name them
                    // properly using the names we detected earlier.
                    $indexedParams = array_values($route["params"]);
                    for ($i = 0; $i < count($route["params"]); $i++) {
                        $request->params[$indexedParams[$i]] = $matches[$i+1];
                    }

                    return $request;
                }
            }
        }
        return false;
    }
}
