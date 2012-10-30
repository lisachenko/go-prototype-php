<?php
/**
 * @author Alexander.Lisachenko
 * @date 30.10.12
 */

namespace Go\Component\Prototype\Library;

use Closure;
use BadMethodCallException;
use ReflectionMethod;

/**
 * Prototype template
 */
trait Prototype
{
    /**
     * Class prototype
     *
     * @var array
     */
    static $prototype = array();

    /**
     * Constructs an object from prototype
     *
     * $this->init method will be used if present
     */
    public function __construct()
    {
        if (is_callable($this->init)) {
            call_user_func_array(array($this, 'init'), func_get_args());
        }
    }

    /**
     * Checks the presence of field for dynamic prototype fields and methods
     *
     * @param string $name Property name
     * @return boolean true if field is set
     */
    public function __isset($name)
    {
        return isset(static::$prototype[$name]);
    }

    /**
     * Accessor for first-class methods and prototype fields
     *
     * @param string $name Field or method name to access
     *
     * @return mixed
     */
    final public function __get($name)
    {
        if (array_key_exists($name, static::$prototype)) {
            return static::$prototype[$name];
        }
        if (method_exists($this, $name)) {
            $method = (new ReflectionMethod($this, $name))->getClosure($this);
            // TODO: enable caching in local properties
            // $this->$name = $method;
            return $method;
        }
        return null;
    }

    /**
     * Method method caller, that can invoke local closures and prototype methods
     *
     * @param string $name Method name
     * @param array $arguments Arguments for method
     *
     * @return mixed
     * @throws \BadMethodCallException If method is unknown
     */
    final public function __call($name, $arguments)
    {
        $method = $this->$name;
        if (!array_key_exists($name, static::$prototype) && !is_callable($method)) {
            throw new BadMethodCallException("Unknown method $name");
        }
        $closure = is_callable($method) ? $method : static::$prototype[$name];
        $rebind  = Closure::bind($closure, $this, get_called_class());
        return call_user_func_array($rebind, $arguments);
    }

    /**
     * Creates a new class from template
     *
     * @param mixed $config New class config
     */
    public static function create(array $config)
    {
        $caller    = get_called_class();
        $className = uniqid($caller);
        $className::$prototype = $caller::$prototype + $config;
        return $className;
    }
}