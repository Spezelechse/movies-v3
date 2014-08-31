<?php
namespace Movies\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('login');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'username',
            'filters'    => array('StringTrim'),
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Username'),
                'label_attributes' => array(
                    'class' => 'col-sm-2 control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'filters'    => array('StringTrim'),
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Password'),
                'label_attributes' => array(
                    'class' => 'col-sm-2 control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'remember',
            'type'  => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => $this->translate('Remember me?'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'btn btn-default',
            ),
        ));

        $this->setInputFilter($this->getInputFilter());
    }

    public function getInputFilter(){
        $inputFilter = new InputFilter();
        $factory     = new InputFactory();

        $inputFilter->add($factory->createInput(array(
            'name'     => 'username',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'password',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        )));

        return $inputFilter;
    }
    
    private function translate($string){
        return $string;
    }
}