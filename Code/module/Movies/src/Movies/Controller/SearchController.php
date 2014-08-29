<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Zend\View\Model\ViewModel;
use Movies\Table\MediaTable;
use ZfTable\Params\AdapterDataTables;
use ZfTable\Options\ModuleOptions;
use ZfTable\AbstractTable;

class SearchController extends BasisController
{
	private $search_value;
	private $basic_select;
	private $sql;

	public function init(){
		parent::init();

        $this->search_value=(int)$this->params()->fromRoute('value',0);

        if($this->search_value==0){
            $this->redirect()->toRoute('movies'); 
        }

        $this->view->search_value = $this->search_value;

    	$this->basic_select=$this->Tables()->medium()->fetchAllForList_Select($this->language);

    	$this->sql = $this->Tables()->medium()->getSql();
	}

	private function createTable($select){
      $table = new MediaTable();
      $table->setShowUrl($this->url()->fromRoute('movies', array('lang'=>$this->language, 'action'=>'show')));
      $table->setLanguage($this->language);

      $table->setAdapter($this->getDbAdapter())
              ->setSource($select)
              ->setParamAdapter($this->getRequest()->getPost());

      $table = $this->htmlResponse($table->render());

      return $table;
	}

	public function indexAction(){
      return $this->view;
	}
	
	public function actorAction(){
		$actor = $this->Tables()->actor()->get($this->search_value);

		if(isset($actor)){
			$this->view->search_value_text = $actor->name;
		}
		else{
			$this->view->search_value_text = '';
		}

    	return $this->view;
	}
	public function ajaxActorAction(){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Actor')
                        ->columns(array('medium_id'))
                        ->where('Medium_has_Actor.actor_id = '.$this->search_value);
                            
        $this->basic_select ->where->in('Medium.id', $select_for_in);

      	return $this->createTable($this->basic_select);
	}
	
	public function directorAction(){
		$director = $this->Tables()->director()->get($this->search_value);

		if(isset($director)){
			$this->view->search_value_text = $director->name;
		}
		else{
			$this->view->search_value_text = '';
		}

	    return $this->view;
	}
	public function ajaxDirectorAction(){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Director')
                        ->columns(array('medium_id'))
                        ->where('Medium_has_Director.director_id = '.$this->search_value);
                            
        $this->basic_select ->where->in('Medium.id', $select_for_in);

      	return $this->createTable($this->basic_select);
	}

	public function publisherAction(){
		$publisher = $this->Tables()->publisher()->get($this->search_value);

		if(isset($publisher)){
			$this->view->search_value_text = $publisher->name;
		}
		else{
			$this->view->search_value_text = '';
		}

      	return $this->view;
	}
	public function ajaxPublisherAction(){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Publisher')
                        ->columns(array('medium_id'))
                        ->where('Medium_has_Publisher.publisher_id = '.$this->search_value);
                            
        $this->basic_select ->where->in('Medium.id', $select_for_in);

      	return $this->createTable($this->basic_select);
	}

	public function genreAction(){
		$genre = $this->Tables()->genre()->get($this->search_value);

		if(isset($genre)){
			$this->view->search_value_text = $genre->getName($this->language);
		}
		else{
			$this->view->search_value_text = '';
		}

      	return $this->view;
	}
	public function ajaxGenreAction(){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Genre')
                        ->columns(array('medium_id'))
                        ->where('Medium_has_Genre.genre_id = '.$this->search_value);
                            
        $this->basic_select ->where->in('Medium.id', $select_for_in);

      	return $this->createTable($this->basic_select);
	}

	public function typeAction(){
		$type = $this->Tables()->type()->get($this->search_value);

		if(isset($type)){
			$this->view->search_value_text = $type->getName($this->language);
		}
		else{
			$this->view->search_value_text = '';
		}
		
      	return $this->view;
	}
	public function ajaxTypeAction(){
        $this->basic_select ->where('type_id = '.$this->search_value);

      	return $this->createTable($this->basic_select);
	}
	
	public function mediumAction(){
		if($this->search_value==1){
			$this->view->search_value_text = 'DVD';
		}
		else{
			$this->view->search_value_text = 'Blu-ray';
		}

      	return $this->view;
	}
	public function ajaxMediumAction(){
        $this->basic_select ->where('dvd_or_bluray = '.$this->search_value);

      	return $this->createTable($this->basic_select);
	}

	public function ownerAction(){
		$owner = $this->Tables()->user()->get($this->search_value);

		if(isset($owner)){
			$this->view->search_value_text = $owner->username;
		}
		else{
			$this->view->search_value_text = '';
		}
		
      	return $this->view;
	}
	public function ajaxOwnerAction(){
        $this->basic_select ->where('owner_id = '.$this->search_value);

      	return $this->createTable($this->basic_select);
	}


	public function fskAction(){
		if(isset($this->search_value)){
			$this->view->search_value_text = $this->search_value;
		}
		else{
			$this->view->search_value_text = '';
		}
		
      	return $this->view;
	}
	public function ajaxFskAction(){
        $this->basic_select ->where('fsk = '.$this->search_value);

      	return $this->createTable($this->basic_select);
	}

	public function advancedAction(){
      return $this->view;
	}
}