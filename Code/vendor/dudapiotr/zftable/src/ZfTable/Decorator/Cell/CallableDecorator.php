<?php
/**
 * ZfTable ( Module for Zend Framework 2)
 *
 * @copyright Copyright (c) 2013 Piotr Duda dudapiotrek@gmail.com
 * @license   MIT License 
 */


namespace ZfTable\Decorator\Cell;

class CallableDecorator extends AbstractCellDecorator
{
    
    /**
     *
     * @var closure
     */
    protected $options;
    
    /**
     * Constructor
     * @param array $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(array $options = array())
    {
        if (!isset($options['callable'])) {
            throw new Exception('Please define closure');
        }
        $this->options = $options;
        
    }

    /**
     * Rendering decorator
     * @param string $context
     * @return string
     */
    public function render($context)
    {
        $closure = $this->options['callable'];
        return $closure($context, $this->getCell()->getActualRow());
    }

}