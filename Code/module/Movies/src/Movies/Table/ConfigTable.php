<?php

namespace Movies\Table;

use ZfTable\AbstractTable;

class ConfigTable extends AbstractTable
{
    private $updateUrl;
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
        'rowAction' => '',
    );
    
     //Definition of headers
    protected $headers = array(
        'id' => array('title' => '#' ) ,
        'name' => array('title' => 'Name', 'filters' => 'text'),
        'data' => array('title' => 'Value', 'editable' => true, 'sortable'=>false),
        'description' => array('title' => 'Description' , 'sortable'=>false) ,
    );

    public function setUpdateUrl($url){
        $this->config['rowAction']=$url;
    }

    public function setTranslator($trans){
        $this->translator=$trans;
    }

    public function init()
    {
        if(isset($this->translator)){
            $this->getHeader('data')->setTitle($this->translator->translate('Value'));
            $this->getHeader('description')->setTitle($this->translator->translate('Description'));
        }

        $this->getRow()->addDecorator('varattr', array('name' => 'data-row' , 'value' => '%s' , 'vars' => array('id')));
    }
    
    protected function initFilters($query)
    {
       if ($value = $this->getParamAdapter()->getQuickSearch()) {
            $query->where("name like '%".$value."%' ");
        }
    }
}