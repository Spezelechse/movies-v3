<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Zend\View\Model\ViewModel;
use Movies\Form\LoginForm;
use Movies\Model\Publisher;
use Movies\Model\Director;
use Movies\Model\Genre;
use Movies\Model\Type;
use imdb;
use imdbsearch;
use imdb_person;

class ImdbController extends BasisController
{
    public function init()
    {
        parent::init();

        if(!$this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('movies', array('lang'=>$this->language, 'action'=>'login')); 
        }
    }

    public function indexAction()
    {
        $this->view->setTerminal(true);
        return $this->view;
    }

    public function ajaxSearchAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $search = new imdbsearch();

            $this->view->searchValue=$post['searchValue'];

            $this->view->results=$search->search($post['searchValue'], null, 10);
            $this->view->setTerminal(true);
            return $this->view;
        }
    }

    public function ajaxImportAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $id = $request->getPost()->toArray()['imdbid'];
            $country = $this->MoviesConfig()->get('country');
            $auto_add = $this->MoviesConfig()->get('imdb_auto_add');

            $imdbData = new imdb($id);
            $titles = $this->processTitles($imdbData->alsoknow(), $imdbData->orig_title());
            $directors = $this->resolveIds($this->processImdbData($imdbData->director(),5), 'Director');
            $publisher = $this->resolveIds($this->processImdbData($imdbData->prodCompany()), 'Publisher');
            $genres = $this->resolveIds($imdbData->genres(), 'Genre');
            $types = $this->resolveIds(array($imdbData->movietype()), 'Type');
            $release = $this->processReleaseInfo($imdbData->releaseInfo(), $country);
            $fsk = (int)(isset($imdbData->mpaa()[$country])) ? $imdbData->mpaa()[$country] : -1;
            $actors = $this->processCast($imdbData->cast(), 20);
            
            $new_directors = $this->saveNewData($directors['missing'], 'Director');
            
            if($auto_add){            
                $new_publisher = $this->saveNewData($publisher['missing'], 'Publisher');
            }
            else{
                $new_publisher = array();
                $this->view->publisher_missing=$publisher['missing'];
            }

            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
            $this->view->genre_missing=$genres['missing'];
            $this->view->type_id_missing=(isset($types['missing'][0])) ? $types['missing'][0] : 0;
            $this->view->setTemplate('movies/imdb/ajax-import.phtml');
            $missing = $viewRender->render($this->view);
            
            return $this->jsonResponse(array(
                'title_en'=>$titles['en'], 
                'title_de'=>$titles['de'],
                'director'=>$directors['found'],
                'publisher'=>$publisher['found'],
                'genre'=>$genres['found'],
                'type_id'=>(isset($types['found'][0])) ? $types['found'][0] : 0,
                'new_publisher'=>$new_publisher,
                'new_director'=>$new_directors,
                'missing_data'=>$missing,
                'fsk'=>$fsk,
                'duration'=>$imdbData->runtime(),
                'premiere'=>$release,
                'actors_text'=>$actors[0],
                'roles_text'=>$actors[1],
                'imdb_url'=>str_replace('akas.','', $imdbData->main_url())
            ));
        }
    }

    private function resolveIds($names, $table){
        $resolved = array();
        $not_resolved = array();

        foreach ($names as $name) {
            $id = (int)$this->Tables()->$table()->getByName($name);
            if($id>0){
                array_push($resolved, $id);
            }   
            else{
                array_push($not_resolved, $name);
            } 
        }

        return array('found'=>$resolved,'missing'=>$not_resolved);
    }

    private function saveNewData($data, $table){
        $saved = array();

        foreach ($data as $value) {
            $class='Movies\Model\\'.$table;
            $obj = new $class();
            $obj->name=$value;

            $id=(int)$this->Tables()->$table()->save($obj);
            array_push($saved, array('id'=>$id,'name'=>$value));
        }

        return $saved;
    }

    private function processTitles($titles, $orig_title){
        $processed=array('en'=>'', 'de'=>'');

        foreach ($titles as $title) {
            if($title['country']=='Germany'||$title['country']=='West Germany'||$title['country']=='Austria'){
                $processed['de']=$title['title'];
            }
            else if($title['country']=='USA'&&empty($title['comments'])){
                $processed['en']=$title['title'];
            }
        }

        if(empty($processed['en'])){
            $processed['en']=$orig_title;
        }

        return $processed;
    }

    private function processCast($cast, $limit=0){
        $processed = array('','');
        $limiter=0;

        foreach ($cast as $actor) {
            $limiter++;
            $actor_data = new imdb_person($actor['imdb']);
            $born = $actor_data->born();

            if(isset($born['year'])){
                $processed[0] .= $actor['name'].'#'.$born['year'].PHP_EOL;
            }
            else{
                $processed[0] .= $actor['name'].PHP_EOL;
            }
            $processed[1] .= preg_replace(array('!\s{2,}!ims','/&nbsp;/','/\((.*)\)/','/\/ .../'),array('','','',''),$actor['role']).PHP_EOL;
            if($limit!=0&&$limiter==$limit){
                break;
            }
        }

        $processed[0]=substr($processed[0],0,strlen($processed[0])-1);
        $processed[1]=substr($processed[1],0,strlen($processed[1])-1);

        return $processed;
    }

    private function processReleaseInfo($releases, $country){
        $processed='';

        foreach ($releases as $release) {
            if($release['country']==$country){
                if((int)$release['mon']<10){
                    $release['mon']='0'.$release['mon'];
                }
                if((int)$release['day']<10){
                    $release['day']='0'.$release['day'];
                }
                $processed=$release['year'].'-'.$release['mon'].'-'.$release['day'];
                return $processed;
            }
        }

        return $processed;
    }

    private function processImdbData($data, $limit=0){
        $processed=array();
        $limiter=0;

        foreach ($data as $value) {
            $limiter++;
            array_push($processed, $value['name']);
            if($limit!=0&&$limiter==$limit){
                break;
            }
        }

        return $processed;
    }   
}