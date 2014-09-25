<?php

namespace Movies\Table;

use ZfTable\AbstractTable;

class UserTable extends AbstractTable
{
    private $editUrl;
    private $deleteUrl;
    private $identity;
    private $translator;
    
    protected $config = array(
        'name' => '',
        'showPagination' => true,
        'showQuickSearch' => true,
        'showItemPerPage' => true,
        'itemCountPerPage' => 10,
        'showColumnFilters' => false,
        'showExportToCSV ' => false,
        'valuesOfItemPerPage' => array(5, 10, 20, 50 , 100 , 200),
        'rowAction' => ''
    );
    
     //Definition of headers
    protected $headers = array(
        'id' => array('title' => '#' ) ,
        'username' => array('title' => 'Username', 'filters' => 'text'),
        'name' => array('title' => 'Name' ),
        'surname' => array('title' => 'Surname' ),
        'email' => array('title' => 'Email' ),
        'actions' => array('title' => 'Actions'),
    );

    public function setEditUrl($url){
        $this->editUrl=$url;
    }

    public function setDeleteUrl($url){
        $this->deleteUrl=$url;
    }

    public function setTranslator($trans){
        $this->translator=$trans;
    }

    public function init()
    {
        if(isset($this->translator)){
            $this->getHeader('username')->setTitle($this->translator->translate('Username'));
            $this->getHeader('surname')->setTitle($this->translator->translate('Surname'));
            $this->getHeader('actions')->setTitle($this->translator->translate('Actions'));
        }
        
        $edit='';
        $delete='';

        if($this->editUrl){
            $edit = '<a class="movies-table-link" href="'.$this->editUrl.'/%s"><button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-pencil"></span></button></a>';
        }
        if($this->deleteUrl){
            $delete = '<a id="user-1" href="'.$this->deleteUrl.'/%s"><button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></button></a>';
        }

        $this->getHeader('actions')->getCell()->addDecorator('template', array(
            'template' => '<div class="text-center">'.$edit.''.$delete.'</div>',
            'vars' => array('id','id')
        ));
    }
    
    protected function initFilters($query)
    {
       if ($value = $this->getParamAdapter()->getQuickSearch()) {
            $query->where("username like '%".$value."%' ");
        }
    }
}