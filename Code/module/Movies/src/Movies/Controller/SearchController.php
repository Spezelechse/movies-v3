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
use Movies\Form\MediumForm;
use Movies\Model\Medium;
use Zend\Filter\StringTrim;
use Zend\Session\Container;

class SearchController extends BasisController
{
	private $search_value;
	private $basic_select;
	private $sql;
	private $search;

	public function init(){
		parent::init();

        if(!$this->MoviesConfig()->get('public')){
        	if(!$this->getAuthService()->hasIdentity()){
            	return $this->redirect()->toRoute('auth', array('lang'=>$this->language, 'action'=>'login'));
          	}
        }

        $this->search_value=(int)$this->params()->fromRoute('value',0);
        $action = $this->params()->fromRoute('action','');

        if($this->search_value==0&&$action!='advanced'&&$action!='ajax-advanced'){
            return $this->redirect()->toRoute('movies', array('lang'=>$this->language)); 
        }

        $this->view->search_value = $this->search_value;

    	$this->basic_select=$this->Tables()->medium()->fetchAllForList_Select($this->language);

    	$this->sql = $this->Tables()->medium()->getSql();

    	$this->session = new Container('search');
	}

	private function createTable($select){
		$params=array(
		'translator' =>$this->Translator()->getTranslator(),
		'basicPath' =>$this->url()->fromRoute('movies'),
		'path' =>$this->getRequest()->getUri()->getPath(),
		);

		$table = new MediaTable($params);
		$table->setShowUrl($this->url()->fromRoute('movies', array('lang'=>$this->language, 'action'=>'show')));
		$table->setLanguage($this->language);

		$table->setAdapter($this->getDbAdapter())
		      ->setSource($select)
		      ->setParamAdapter($this->getRequest()->getPost());

		$table = $this->htmlResponse($table->render());

		return $table;
	}

	public function indexAction(){
        return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
	}
	
	public function actorAction(){
		try{
			$actor = $this->Tables()->actor()->get($this->search_value);
      	}
      	catch(\Exception $e){
      		return $this->redirect()->toRoute('movies', array('lang'=>$this->language)); 
      	}

		if(isset($actor)){
			$this->view->search_value_text = $actor->name;
		}
		else{
			$this->view->search_value_text = '';
		}

    	return $this->view;
	}
	public function ajaxActorAction(){                            
        $this->basic_select ->where->in('Medium.id', $this->searchActor($this->search_value));

      	return $this->createTable($this->basic_select);
	}
	public function searchActor($id){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Actor')
                        ->columns(array('medium_id'))
                        ->where('Medium_has_Actor.actor_id = '.$id);
        return $select_for_in;
	}
	public function searchRole($text){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Actor')
                        ->columns(array('medium_id'))
                        ->where->like('Medium_has_Actor.role',$text);
        return $select_for_in;
	}
	
	public function directorAction(){
		try{
			$director = $this->Tables()->director()->get($this->search_value);
      	}
      	catch(\Exception $e){
      		return $this->redirect()->toRoute('movies', array('lang'=>$this->language)); 
      	}

		if(isset($director)){
			$this->view->search_value_text = $director->name;
		}
		else{
			$this->view->search_value_text = '';
		}

	    return $this->view;
	}
	public function ajaxDirectorAction(){                            
        $this->basic_select ->where->in('Medium.id', $this->searchDirector($this->search_value));

      	return $this->createTable($this->basic_select);
	}
	public function searchDirector($id){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Director')
                        ->columns(array('medium_id'))
                        ->where('Medium_has_Director.director_id = '.$id);
        return $select_for_in;
	}

	public function publisherAction(){
		try{
			$publisher = $this->Tables()->publisher()->get($this->search_value);
      	}
      	catch(\Exception $e){
      		return $this->redirect()->toRoute('movies', array('lang'=>$this->language)); 
      	}

		if(isset($publisher)){
			$this->view->search_value_text = $publisher->name;
		}
		else{
			$this->view->search_value_text = '';
		}

      	return $this->view;
	}
	public function ajaxPublisherAction(){                            
        $this->basic_select ->where->in('Medium.id', $this->searchPublisher($this->search_value));

      	return $this->createTable($this->basic_select);
	}
	public function searchPublisher($id){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Publisher')
                        ->columns(array('medium_id'))
                        ->where('Medium_has_Publisher.publisher_id = '.$id);
        return $select_for_in;
	}

	public function genreAction(){
		try{
			$genre = $this->Tables()->genre()->get($this->search_value);
      	}
      	catch(\Exception $e){
      		return $this->redirect()->toRoute('movies', array('lang'=>$this->language)); 
      	}

		if(isset($genre)){
			$this->view->search_value_text = $genre->getName($this->language);
		}
		else{
			$this->view->search_value_text = '';
		}

      	return $this->view;
	}
	public function ajaxGenreAction(){                            
        $this->basic_select ->where->in('Medium.id', $this->searchGenre($this->search_value));

      	return $this->createTable($this->basic_select);
	}
	public function searchGenre($id){
        $select_for_in = $this->sql->select();
        $select_for_in  ->from('Medium_has_Genre')
                        ->columns(array('medium_id'))
                        ->where('Medium_has_Genre.genre_id = '.$id);
        return $select_for_in;
	}

	public function typeAction(){
		try{
			$type = $this->Tables()->type()->get($this->search_value);
      	}
      	catch(\Exception $e){
      		return $this->redirect()->toRoute('movies', array('lang'=>$this->language)); 
      	}

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
		try{
			$owner = $this->Tables()->user()->get($this->search_value);
      	}
      	catch(\Exception $e){
      		return $this->redirect()->toRoute('movies', array('lang'=>$this->language)); 
      	}

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
		$request = $this->getRequest();
		$form = new MediumForm();
		$medium = new Medium();

		$form->get('submit')->setValue($this->translate('Search'));
        $form->get('genre')->setValueOptions($this->Tables()->genre()->fetchAllForSelect($this->language));
        $form->get('director')->setValueOptions($this->Tables()->director()->fetchAllForSelect());
        $form->get('publisher')->setValueOptions($this->Tables()->publisher()->fetchAllForSelect());
        $form->get('type_id')->setValueOptions($this->Tables()->type()->fetchAllForSelect($this->language));
        $form->get('owner_id')->setValueOptions($this->Tables()->user()->fetchAllForSelect());

        if ($request->isPost()) {
        	$data=$request->getPost()->toArray();

        	$form->setInputFilter($medium->getInputFilter('none'));
        	$form->setData($data);
        	if ($form->isValid()){
				if(!empty($data["title_en"])){
        			$this->basic_select ->where->like('Medium.title_en','%'.$data["title_en"].'%')->or->like('Medium.title_de','%'.$data["title_en"].'%');
				}
				if(!empty($data["content_en"])){
        			$this->basic_select ->where->like('Medium.content_en','%'.$data["content_en"].'%')->or->like('Medium.content_de','%'.$data["content_en"].'%');
				}
				if(!empty($data["type_id"])){
        			$this->basic_select ->where('Medium.type_id='.$data["type_id"]);
				}
				if(!empty($data["owner_id"])){
        			$this->basic_select ->where('Medium.owner_id='.$data["owner_id"]);
				}
				if(!empty($data["duration"])){
					$comparator ='=';
					if($data["duration_comparator"]=='>'){
						$comparator ='>';
					}
					else if($data["duration_comparator"]=='<'){
						$comparator ='<';
					}

        			$this->basic_select ->where('Medium.duration '.$comparator.$data["duration"]);
        		}
				if(!empty($data["fsk"])){
        			$this->basic_select ->where('Medium.fsk='.$data["fsk"]);
        		}
				if(!empty($data["premiere"])){
					$comparator ='=';
					if($data["premiere_comparator"]=='>'){
						$comparator ='>';
					}
					else if($data["premiere_comparator"]=='<'){
						$comparator ='<';
					}
        			$this->basic_select ->where('Medium.premiere '.$comparator.'\''.$data["premiere"].'\'');
        		}
				if(!empty($data["num_disks"])){
        			$this->basic_select ->where('Medium.num_disks='.$data["num_disks"]);
        		}
				if(!empty($data["dvd_or_bluray"])){
        			$this->basic_select ->where('Medium.dvd_or_bluray='.$data["dvd_or_bluray"]);
        		}
				if(!empty($data["genre"])){
					foreach ($data["genre"] as $genre_id) {
        				$this->basic_select ->where->in('Medium.id', $this->searchGenre($genre_id));
					}
				}
				if(!empty($data["director"])){
					foreach ($data["director"] as $director_id) {
        				$this->basic_select ->where->in('Medium.id', $this->searchDirector($director_id));
					}
				}
				if(!empty($data["publisher"])){
					foreach ($data["publisher"] as $publisher_id) {
        				$this->basic_select ->where->in('Medium.id', $this->searchPublisher($publisher_id));
					}
				}
				if(!empty($data["actors_text"])){
					$actor_ids=$this->Tables()->actor()->getActorIdsFromText($data["actors_text"]);
					foreach ($actor_ids as $actor_id) {
        				$this->basic_select ->where->in('Medium.id', $this->searchActor($actor_id));
					}
				}
				if(!empty($data["roles_text"])){
					$roles = explode(PHP_EOL, $data["roles_text"]);
        			$trim = new StringTrim();

					foreach ($roles as $role) {
        				$this->basic_select ->where->in('Medium.id', $this->searchRole($trim($role)));
					}
				}

				if($this->session){
					$this->session->advanced_select=$this->basic_select;
				}

	        	$this->view->showResult =  true;
        	}
            else{
                $this->showFormMessages($form);
            }
        }

        $this->view->form = $form;

      	return $this->view;
	}

	public function ajaxAdvancedAction(){
		if($this->session&&$this->session->advanced_select){
			return $this->createTable($this->session->advanced_select);
		}

		return $this->createTable($this->basic_select);
	}
}
