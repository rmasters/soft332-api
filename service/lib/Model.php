<?php
/**
 * Base model class that implements some accessor methods and array casting
 * (for JSON output).
 *
 * Combines nicely with the Mapper base for persisting these models using ORM.
 */

/**
 * Model base-class
 *
 * Extend this class and declare each property as protected to ensure that
 * the accessor methods and toArray method work correctly.
 *
 * If you have any extra logic (e.g. validation, fetching another model from an
 * ID) to perform, create a getName or setName method in the child class. For
 * example:
 *
 * <code>
 * <?php
 * class User extends Model {
 *     protected $id;
 *     protected $parent;
 *     
 *     // Validation example
 *     public function setId($id) { $this->id = (int) $id; }
 *     
 *     // ORM inflation example
 *     public function getParent() {
 *         if (!$this->parent instanceof Model\Parent) {
 *             $mapper = new Mapper\Post;
 *             $parent = $mapper->find($this->parent);
 *         }
 *         return $this->parent;
 *     }
 * }
 * ?>
 * </code>
 */
class Model
{
    /**
     * Instantiate the model
     *
     * @param array|null An associative array of values to set in the model.
     */
    public function __construct(array $params = null) {
        if (is_array($params)) {
            $this->fromArray($params);
        }
    }

    /**
     * Set accessor
     *
     * @param string Name of parameter
     * @param mixed Value to store
     * @throws Exception If no setter method found,
     *
     * Attempts to find a setter method declared in the child class and uses it
     * if one exists. Alternatively stores the value in the property.
     *
     * Usage:
     * <code>
     * <?php
     * $model->foo = "bar";
     * ?>
     * </code>
     */
    public function __set($name, $value) {
        // Call setName where found
        $method = "set" . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            throw new \Exception("Setting invalid property '$name' of model '" . get_class($this) . "'.");
        }
    }

    /**
     * Get accessor
     *
     * @param string Name of parameter
     * @return mixed
     * @throws Exception If no getter method found,
     *
     * Attempts to find a getter method declared in the child class and uses it
     * if one exists. Alternatively return the value in the property.
     *
     * It is possible (and useful) to define a setter method in the child class
     * for a property that does not exist. E.g:
     *
     * <code>
     * <?php
     * class Post extends Model {
     *     protected $post;
     *     public function getPost() {
     *         return some_filter($this->post);
     *     }
     *     public function getPostRaw() {
     *         return $this->post;
     *     }
     * }
     * ?>
     * </code>
     */
    public function __get($name) {
        // Method in form getName
        $method = "get" . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        } else if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new \Exception("Getting invalid property '$name' of model '" . get_class($this) . "'.");
        }
    }

    /**
     * Check whether the property is set.
     *
     * @param string Name of property
     * @return bool True if property is not null
     *
     * Check for null value returned from __get().
     */
    public function __isset($name) {
        return !is_null($this->$name);
    }

    /**
     * Unset a property
     *
     * @param string Name of property
     *
     * Set the property's value to null (in case of a pseudo-property).
     */
    public function __unset($name) {
        $this->$name = null;
    }

    /**
     * Catch setName and getName methods and redirect to a __get or __set
     * respectively.
     *
     * @param string Method called
     * @param array Parameters passed to the method
     * @return mixed|void The value of the property (in __get) or void (in __set)
     * @throws Exception If no method was found.
     */
    public function __call($method, array $params) {
        // Catch methods beginning with get or set with at least one more char
        if (strlen($method) > 3) {
            $prefix = substr($method, 0, 3);
            $name = substr($method, 3);

            if ($prefix == "set" && count($params) == 1) {
                $this->$name = $params[0];
            } else if ($prefix == "get") {
                return $this->$name;
            }
        }

        throw new \Exception("Invalid method '$method' of model '" . get_class($this) . "'.");
    }

    /**
     * Return a string representation of the model
     *
     * @return string The model name, in angle brackets
     */
    public function __toString() {
        return "<" . get_class($this) . ">";
    }

    /**
     * Load values into the model from an associative array
     *
     * @param array Associative array of parameters
     *
     * Values are passed through __set.
     */
    public function fromArray(array $params) {
        foreach ($params as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Produce an array of the model's parameters
     *
     * @param bool Whether to use a whitelist (below).
     *
     * This method recursively produces an array of the model's attributes. It
     * can optionally (and by default) use a whitelist defined in the child
     * class. This is done by defining a method as below:
     *
     * <code>
     * <?php
     * public function toCleanArray() {
     *     $vars = get_object_vars($this);
     *     unset($vars["password"], $vars["secret"]);
     *     return $vars;
     * }
     * ?>
     * </code>
     *
     * Properties of the model that are also subclasses of Model also have
     * their toArray() method called (which will in turn, call toCleanArray()
     * if present).
     */
    public function toArray($clean = true) {
        if ($clean && method_exists($this, "toCleanArray")) {
            // Use a clean array if enabled and present
            $vars = $this->toCleanArray();
        } else {
            // Otherwise, give whatever properties we can get from this scope
            $vars = get_object_vars($this);
        }

        foreach ($vars as $k => $v) {
            // Ensure a getName() method is called if one exists
            $v = $this->__get($k);

            // Recursively build the array if the element is a model instance
            if ($v instanceof self) {
                $vars[$k] = $v->toArray();
            }
        }

        return $vars;
    }
}
