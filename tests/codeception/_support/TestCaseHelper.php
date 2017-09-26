<?php
namespace Codeception\Module;

use ReflectionObject;

/**
 * Helper class with static methods to be used in tests
 */
class TestCaseHelper extends \Codeception\Module
{
    /**
     * Set value for private/protected properties in object
     *
     * @param mixed Object Instance of any class with target property
     * @param $property string Name of property which we want to set
     * @param $value mixed New value of property
     */
    public static function setProperty($object, $property, $value)
    {
        $rClass = new ReflectionObject($object);
        $rProp = $rClass->getProperty($property);
        $rProp->setAccessible(true);
        $rProp->setValue($object, $value);
    }

    /**
     * Get private/protected value from object
     *
     * @param $object mixed Instance of any class with target property
     * @param $property string Name of property which we need to return
     * @return mixed
     */
    public static function getProperty($object, $property)
    {
        $rClass = new ReflectionObject($object);
        $rProp = $rClass->getProperty($property);
        $rProp->setAccessible(true);

        return $rProp->getValue($object);
    }
}