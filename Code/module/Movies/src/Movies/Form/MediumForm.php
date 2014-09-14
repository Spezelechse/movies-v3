<?php
namespace Movies\Form;

use Zend\Form\Form;

class MediumForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('medium');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'title_de',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('German'),
                'label_attributes' => array(
                    'class' => 'col-sm-1 control-label',
                ),
            ),
        ));        
        $this->add(array(
            'name' => 'title_en',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('English'),
                'label_attributes' => array(
                    'class' => 'col-sm-1 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'content_de',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'rows' => 10,
            ),
            'options' => array(
                'label' => $this->translate('German'),
                'label_attributes' => array(
                    'class' => 'col-sm-1 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'content_en',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'rows' => 10,
            ),
            'options' => array(
                'label' => $this->translate('English'),
                'label_attributes' => array(
                    'class' => 'col-sm-1 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'actors_text',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'rows' => 10,
            ),
            'options' => array(
                'label' => $this->translate('Actor'),
            ),
        ));
        $this->add(array(
            'name' => 'roles_text',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'rows' => 10,
            ),
            'options' => array(
                'label' => $this->translate('Role'),
            ),
        ));
        $this->add(array(
            'name' => 'genre',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'form-control',
                'multiple' => 'multiple',
                'id' => 'genre-select',
            ),
            'options' => array(
                'label' => $this->translate('Genre'),
                'label_attributes' => array(
                    'class' => 'col-sm-12 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'publisher',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'form-control',
                'multiple' => 'multiple',
                'id' => 'publisher-select',
            ),
            'options' => array(
                'label' => $this->translate('Publisher'),
                'label_attributes' => array(
                    'class' => 'col-sm-12 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'director',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'form-control',
                'multiple' => 'multiple',
                'id' => 'director-select',
            ),
            'options' => array(
                'label' => $this->translate('Director'),
                'label_attributes' => array(
                    'class' => 'col-sm-12 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'type_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'type-select',
            ),
            'options' => array(
                'label' => $this->translate('Type'),
                'label_attributes' => array(
                    'class' => 'col-sm-12 control-label',
                ),
                'empty_option' => '-',
            ),
        ));
        $this->add(array(
            'name' => 'owner_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Owner'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
                'empty_option' => '-',
            ),
        ));
        $this->add(array(
            'name' => 'fsk',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('FSK'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
                'value_options' => array(
                                    0 => 0,
                                    6 => 6,
                                    12 => 12,
                                    16 => 16,
                                    18 => 18,
                ),
                'empty_option' => '-',
            ),
        ));
        $this->add(array(
            'name' => 'premiere',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'premiere',
            ),
            'options' => array(
                'label' => $this->translate('Premiere'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'duration',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Duration (min.)'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'num_disks',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Num. disks'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'dvd_or_bluray',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Medium'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
                'value_options' => array(
                                    1 => 'DVD',
                                    2 => 'Blu-ray'
                ),
                'empty_option' => '-',
            ),
        ));

        $this->add(array(
            'name' => 'imdb_url',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('IMDb url'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'cover_source',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => $this->translate('Source'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'cover_file',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(
                'class' => 'col-sm-12',
            ),
            'options' => array(
                'label' => $this->translate('File'),
                'label_attributes' => array(
                    'class' => 'col-sm-4 control-label',
                ),
            ),
        ));

        $this->add(array( 
            'name' => 'csrf', 
            'type' => 'Zend\Form\Element\Csrf', 
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