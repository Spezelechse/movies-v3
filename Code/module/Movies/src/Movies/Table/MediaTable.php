<?php

namespace Movies\Table;

use ZfTable\AbstractTable;

class MediaTable extends AbstractTable
{
    private $showUrl;
    private $language;
    private $translator;
    private $fileNeeded;
    private $detailsNeeded;
    
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
        'cover_file' => array(),
        'content' => array(),
        'duration' => array(),
        'dvd_or_bluray' => array(),
        'type' => array(),
    );

    public function __construct($data){
        $this->config['name']=$data;

        $this->config['templateMap'] = array(
            'paginator-slide' => __DIR__.'/../../../view/zf-table/templates/slide-paginator.phtml',
            'default-params' => __DIR__.'/../../../view/zf-table/templates/default-params.phtml',
            'container' => __DIR__.'/../../../view/zf-table/templates/container-b3.phtml',
            'data-table-init' => __DIR__.'/../../../view/zf-table/zf-table/templates/data-table-init.phtml',
            'custom-pic-only' => __DIR__.'/../../../view/zf-table/templates/custom-pic-only.phtml',
            'custom-list-detail' => __DIR__.'/../../../view/zf-table/templates/custom-list-detail.phtml',
        );
    }

    public function setShowUrl($url){
        $this->showUrl=$url;
    }

    public function setLanguage($lang){
        $this->language=$lang;
    }

    public function setTranslator($trans){
        $this->translator=$trans;
    }

    public function setFileNeeded($needed){
        $this->fileNeeded=$needed;
    }

    public function setDetailsNeeded($needed){
        $this->detailsNeeded=$needed;
    }

    public function init()
    {
        if(!$this->fileNeeded){
            unset($this->headers['cover_file']);
        }

        if(!$this->detailsNeeded){
            unset($this->headers['content']);
            unset($this->headers['duration']);
            unset($this->headers['dvd_or_bluray']);
            unset($this->headers['type']);
        }
        
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

    public function render()
    {
        $style=$this->getParamAdapter()->getPureValueOfFilter('listStyle');
        $table='';

        if($style=='pic'){
            $this->setFileNeeded(true);
            $table = parent::render('custom', 'custom-pic-only');
        }
        else if($style=='detail'){
            $this->setFileNeeded(true);
            $this->setDetailsNeeded(true);
            $table = parent::render('custom', 'custom-list-detail');
        }
        else{
            $this->setFileNeeded(false);
            $table = parent::render();
        }

        return $table;
    }
}