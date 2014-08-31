<?php

namespace Movies\Table;

use ZfTable\AbstractTable;

class MediaTable extends AbstractTable
{
    private $showUrl;
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
        'id' => array('title' => '#' ) ,
        'title' => array('title' => 'Title', 'filters' => 'text'),
        'genres' => array('title' => 'Genre' ),
    );

    public function setShowUrl($url){
        $this->showUrl=$url;
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
        
        if($this->showUrl)
        {
            $this->getHeader('title')->getCell()->addDecorator('link', array(
                'url' => $this->showUrl.'/%s',
                'vars' => array('id')
            ));
        }
    }
    
    protected function initFilters(\Zend\Db\Sql\Select $query)
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