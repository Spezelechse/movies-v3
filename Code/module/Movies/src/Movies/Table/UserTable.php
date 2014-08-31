<?php

namespace Movies\Table;

use ZfTable\AbstractTable;

class UserTable extends AbstractTable
{
    private $editUrl;
    private $deleteUrl;
    private $identity;
    
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

    public function setIdentity($ident){
        $this->identity=$ident;
    }

    public function init()
    {
        $edit='';
        $delete='';

        if($this->editUrl&&$this->identity->hasRight('user','edit')){
            $edit = '<a class="movies-table-link" href="'.$this->editUrl.'/%s"><button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-pencil"></span></button></a>';
        }
        if($this->deleteUrl&&$this->identity->hasRight('user','delete')){
            $delete = '<a id="user-1" href="'.$this->deleteUrl.'/%s"><button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></button></a>';
        }

        $this->getHeader('actions')->getCell()->addDecorator('template', array(
            'template' => '<div class="text-center">'.$edit.''.$delete.'</div>',
            'vars' => array('id','id')
        ));
    }
    
    protected function initFilters(\Zend\Db\Sql\Select $query)
    {
       if ($value = $this->getParamAdapter()->getQuickSearch()) {
            $query->where("username like '%".$value."%' ");
        }
    }
}