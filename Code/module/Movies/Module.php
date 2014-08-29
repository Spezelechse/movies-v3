<?php
/**
 * Module Bootstrap
 *
 * @package Movies
 */
namespace Movies;

use Movies\Model\Actor;
use Movies\Model\Blacklist;
use Movies\Model\Borrower;
use Movies\Model\Config;
use Movies\Model\Cover;
use Movies\Model\Director;
use Movies\Model\Genre;
use Movies\Model\Medium;
use Movies\Model\Publisher;
use Movies\Model\Type;
use Movies\Model\User;
use Movies\Model\ActorTable;
use Movies\Model\BlacklistTable;
use Movies\Model\BorrowerTable;
use Movies\Model\ConfigTable;
use Movies\Model\CoverTable;
use Movies\Model\DirectorTable;
use Movies\Model\GenreTable;
use Movies\Model\MediumTable;
use Movies\Model\PublisherTable;
use Movies\Model\TypeTable;
use Movies\Model\UserTable;
use Movies\Model\MoviesAuthStorage;
use Movies\View\Helper\ConfigHelper;
use Movies\Controller\MoviesController;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as DbTableAuthAdapter;
use Zend\Crypt\Password\Bcrypt;

class Module
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
                array( 
                    'SimpleImage'  => __DIR__ . '/lib/SimpleImage.php',
                ),
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'movies_config' => function($sm) {
                    $vh = new ConfigHelper();
                    $vh->setServiceLocator($sm->getServiceLocator());

                    return $vh;
                },
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Movies\Model\MoviesAuthStorage' => function($sm){
                    return new MoviesAuthStorage('movies'); 
                },
                'AuthService' => function($sm) {
                    $dbAdapter           = $sm->get('Zend\Db\Adapter\Adapter');
                    $callback = function($hash, $input) {
                        return (new Bcrypt())->verify($input, $hash);
                    };
                    $dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter,
                                                      'User','username','password', $callback);
                     
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Movies\Model\MoviesAuthStorage'));
                      
                    return $authService;
                },
                'Movies\Model\ActorTable' =>  function($sm) {
                    $tableGateway = $sm->get('ActorTableGateway');
                    $table = new ActorTable($tableGateway);
                    return $table;
                },
                'ActorTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Actor());
                    return new TableGateway('Actor', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\BlacklistTable' =>  function($sm) {
                    $tableGateway = $sm->get('BlacklistTableGateway');
                    $table = new BlacklistTable($tableGateway);
                    return $table;
                },
                'BlacklistTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Blacklist());
                    return new TableGateway('Blacklist', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\BorrowerTable' =>  function($sm) {
                    $tableGateway = $sm->get('BorrowerTableGateway');
                    $table = new BorrowerTable($tableGateway);
                    return $table;
                },
                'BorrowerTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Borrower());
                    return new TableGateway('Borrower', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\ConfigTable' =>  function($sm) {
                    $tableGateway = $sm->get('ConfigTableGateway');
                    $table = new ConfigTable($tableGateway);
                    return $table;
                },
                'ConfigTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Config());
                    return new TableGateway('Config', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\CoverTable' =>  function($sm) {
                    $tableGateway = $sm->get('CoverTableGateway');
                    $table = new CoverTable($tableGateway);
                    return $table;
                },
                'CoverTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Cover());
                    return new TableGateway('Cover', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\DirectorTable' =>  function($sm) {
                    $tableGateway = $sm->get('DirectorTableGateway');
                    $table = new DirectorTable($tableGateway);
                    return $table;
                },
                'DirectorTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Director());
                    return new TableGateway('Director', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\GenreTable' =>  function($sm) {
                    $tableGateway = $sm->get('GenreTableGateway');
                    $table = new GenreTable($tableGateway);
                    return $table;
                },
                'GenreTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Genre());
                    return new TableGateway('Genre', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\MediumTable' =>  function($sm) {
                    $tableGateway = $sm->get('MediumTableGateway');
                    $table = new MediumTable($tableGateway);
                    return $table;
                },
                'MediumTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Medium());
                    return new TableGateway('Medium', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\PublisherTable' =>  function($sm) {
                    $tableGateway = $sm->get('PublisherTableGateway');
                    $table = new PublisherTable($tableGateway);
                    return $table;
                },
                'PublisherTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Publisher());
                    return new TableGateway('Publisher', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\TypeTable' =>  function($sm) {
                    $tableGateway = $sm->get('TypeTableGateway');
                    $table = new TypeTable($tableGateway);
                    return $table;
                },
                'TypeTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Type());
                    return new TableGateway('Type', $dbAdapter, null, $resultSetPrototype);
                },
                'Movies\Model\UserTable' =>  function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('User', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
