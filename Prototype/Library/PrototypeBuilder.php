<?php
/**
 * @author Alexander.Lisachenko
 * @date 30.10.12
 */

namespace Go\Component\Prototype\Library;

use ReflectionFunction;

/**
 * Prototype builder creates classes on the "fly" during autoload.
 */
class PrototypeBuilder
{
    /**
     * Initialize prototype class loader
     */
    public static function init()
    {
        // Prototype loader
        spl_autoload_register(function ($className) {
            // TODO: add support for namespaces
            $template = <<<EOT
use Go\Component\Prototype\Library\Object as PrototypeObject;

class $className extends PrototypeObject {}

EOT;
            // Replace this hack with filtered include
            eval($template);
            // If function with the same name is present, then use it as constructor for object
            if (function_exists($className)) {
                $func = new ReflectionFunction($className);
                $className::$prototype['init'] = $func->getClosure();
            }
        });
    }
}