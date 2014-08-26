<?php
/**
 * ZfTable ( Module for Zend Framework 2)
 *
 * @copyright Copyright (c) 2013 Piotr Duda dudapiotrek@gmail.com
 * @license   MIT License 
 */


namespace ZfTable;

use ZfTable\AbstractCommon;

abstract class AbstractElement extends AbstractCommon
{

    /**
     * Array of attributes
     * @var array
     */
    protected $attributes = array();

    /**
     * Array of class
     * @var array
     */
    protected $class = array();

    /**
     * Colestions of decorators 
     * @var array
     */
    protected $decorators = array();
    
    
    /**
     * Array of vars class
     * @var array
     */
    protected $varClass = array();
    
    /**
     * Array of vars attr
     * @var array
     */
    protected $varAttr= array();
    
    /**
     * Add new class to element
     * @param string $class
     * @return \ZfTable\AbstractElement
     */
    public function addClass($class)
    {
        if (!in_array($class, $this->class)) {
            $this->class[] = $class;
        }
        return $this;
    }

    /**
     * Remove class from element
     * @param string $class
     @ return \ZfTable\AbstractElement
     */
    public function removeClass($class)
    {
        if (($key = array_search($class, $this->class)) !== false) {
            unset($this->class[$key]);
        }
        return $this;
    }
    
     /**
     * Add new var class to element
     * @param string $class
     * @return \ZfTable\AbstractElement
     */
    public function addVarClass($class)
    {
        if (!in_array($class, $this->varClass)) {
            $this->varClass[] = $class;
        }
        return $this;
    }
    
    /**
     * Add new var class to element
     * @param string $class
     * @return \ZfTable\AbstractElement
     */
    public function addVarAttr($name, $value)
    {
        if (!in_array($name, $this->varAttr)) {
            $this->varAttr[$name] = $value;
        }
        return $this;
    }
    
    /**
     * Add new attribute to table, header, column or rowset
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function addAttr($name, $value)
    {
        if (!in_array($name, $this->attributes)) {
            $this->attributes[$name] = $value;
        }
        return $this;
    }

    /**
     * Get string class from array
     * @return string
     */
    public function getClassName()
    {
        $className = '';
        if (count($this->class)) {
            $className = implode(' ', array_values($this->class));
        }
        if (count($this->varClass)) {
            $className .= ' ';
            $className .= implode(' ', array_values($this->varClass));
        }
        return $className;
    }

    /**
     * Get attributes as a string
     * @return NULL|string
     */
    public function getAttributes()
    {
        $ret = array();
        if (count($this->attributes)) {
            foreach ($this->attributes as $name => $value) {
                $ret[] = sprintf($name . '="%s"', $value);
            }
        }
         if (count($this->varAttr)) {
            foreach ($this->varAttr as $name => $value) {
                $ret[] = sprintf($name . '="%s"', $value);
            }
        }
        if (strlen($className = $this->getClassName())) {
            $ret[] = sprintf('class="%s"', $className);
        }
        return ' ' . implode(' ', $ret);
    }

     /**
     * Clearing var classes
     */
    public function clearVar()
    {
        $this->varClass = array();
        $this->varAttr = array();
    }
    
    /**
     * Get collestions of decoratos
     * @return array
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * 
     * @param type $decorators
     * @return \ZfTable\AbstractElement
     */
    public function setDecorators($decorators)
    {
        $this->decorators = $decorators;
        return $this;
    }

    /**
     * 
     * @param \ZfTable\Decorator\AbstractDecorator $options
     */
    protected function attachDecorator($decorator)
    {
        $this->decorators[] = $decorator;
    }

}
