<?php

namespace Movies\Table;

use ZfTable\AbstractTable;

class ExportTable extends AbstractTable
{
    private $language;
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
        'title' => array('title' => 'Title', 'filters' => 'text'),
        'select' => array('title' => '', 'sortable'=>false),
    );

    public function __construct($data){
        $this->config['name']=$data;

        $this->config['templateMap'] = array(
            'paginator-slide' => __DIR__.'/../../../view/zf-table/templates/slide-paginator.phtml',
            'default-params' => __DIR__.'/../../../view/zf-table/templates/default-params.phtml',
            'container' => __DIR__.'/../../../view/zf-table/templates/container-b3.phtml',
            'data-table-init' => __DIR__.'/../../../view/zf-table/zf-table/templates/data-table-init.phtml',
        );
    }

    public function setLanguage($lang){
        $this->language=$lang;
    }

    public function setTranslator($trans){
        $this->translator=$trans;
    }

    public function init()
    {        
        if(isset($this->translator)){
            $this->getHeader('title')->setTitle($this->translator->translate('Title'));
        }

        $this->getHeader('select')->getCell()->addDecorator('template', array(
            'template' => '<div class="text-center"><input type="checkbox" class="export-select" value="%s" data-title="%s" name="export_ids"></input></div>',
            'vars' => array('id','title')
        ));
    }
    
    protected function initFilters($query)
    {
       if ($value = $this->getParamAdapter()->getQuickSearch()) {
            $lang='en';
            if(isset($this->language)){
                $lang=$this->language;
            }

            $query->where("title_".$lang." like '%".$value."%'");
        }
    }
}