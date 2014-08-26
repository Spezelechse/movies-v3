<?php

namespace Movies\Table;

use ZfTable\AbstractTable;

class ConfigTable extends AbstractTable
{
    private $updateUrl;
    
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
        'data' => array('title' => 'Value', 'editable' => true),
    );

    public function setUpdateUrl($url){
        $this->config['rowAction']=$url;
    }

    public function init()
    {
        $this->getRow()->addDecorator('varattr', array('name' => 'data-row' , 'value' => '%s' , 'vars' => array('id')));
    }
    
    protected function initFilters(\Zend\Db\Sql\Select $query)
    {
       if ($value = $this->getParamAdapter()->getQuickSearch()) {
            $query->where("name like '%".$value."%' ");
        }
    }
}