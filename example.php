<?php
/**
 * @author Alexander.Lisachenko
 * @date 30.10.12
 */

use Go\Component\Prototype\Library\PrototypeBuilder;

// Loading block
{
    include 'Prototype/Library/Prototype.php';
    include 'Prototype/Library/Object.php';
    include 'Prototype/Library/PrototypeBuilder.php';

    PrototypeBuilder::init();
}

// Constructor for Object class
{

    function Object($message) {

        $this->hello = $message;

        $this->message = function () {
            echo $this->hello;
        };

        $this->welcome = function ($user) {
            echo $this->hello, ", ", $user;
        };
    }
}

$object = new Object('Hello');

$object->welcome('User'); // Hello, User
Object::$prototype += [
    'text'     => 'Some text',
    'showText' => function() {
        echo $this->text;
    }
];
$object->showText(); // Some text

$testObject = $object::create([
    'wow' => function () {
        $this->hello .= 'wow';
        echo $this->hello;
    }
]);
$test = new $testObject('Test hello');
$test->wow();