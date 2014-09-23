<?php
namespace Movies\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Name'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'surname',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Surname'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Username'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Email'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Password'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'password-repeat',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Password repeat'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'medium-rights',
            'type'  => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'label' => $this->translate('Medium Rights'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
                'value_options' => array(
                                    0 => $this->translate('Add'),
                                    1 => $this->translate('Edit'),
                                    2 => $this->translate('Delete'),
                                    3 => $this->translate('Import'),
                                    4 => $this->translate('Export'),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'user-rights',
            'type'  => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'label' => $this->translate('User Rights'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
                'value_options' => array(
                                    '0' => $this->translate('Add'),
                                    '1' => $this->translate('Edit'),
                                    '2' => $this->translate('Delete'),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'page-rights',
            'type'  => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'label' => $this->translate('Page Rights'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
                'value_options' => array(
                                    '0' => $this->translate('Set up'),
                ),
            ),
        ));

        $this->add(array( 
            'name' => 'csrf', 
            'type' => 'Zend\Form\Element\Csrf',
            'options' => array(
                 'csrf_options' => array(
                         'timeout' => 1800
                 )
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
    }

    private function translate($string){
        return $string;
    }
}