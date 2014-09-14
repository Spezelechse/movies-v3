<?php
/**
 * 
 * @package Movies
 */
return array(
    'controllers' => array(
        'invokables' => array(
            'MoviesController' => 'Movies\Controller\MoviesController',
            'AdminController' => 'Movies\Controller\AdminController',
            'MediumController' => 'Movies\Controller\MediumController',
            'AuthController' => 'Movies\Controller\AuthController',
            'SearchController' => 'Movies\Controller\SearchController',
            'UserController' => 'Movies\Controller\UserController',
            'ImdbController' => 'Movies\Controller\ImdbController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
           'Tables' => 'Movies\Controller\Plugin\TablesPlugin',
           'MoviesConfig' => 'Movies\Controller\Plugin\ConfigPlugin',
           'Translator' => 'Movies\Controller\Plugin\TranslatorPlugin',
        )
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'movies' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang[/:action[/:id]]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'lang'   => '[a-z][a-z]',
                    ),
                    'defaults' => array(
                        'controller' => 'MoviesController',
                        'action'     => 'index',
                        'id'         => '0',
                        'lang'       => 'en',
                    ),
                ),
            ),
            'auth' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/:lang/auth[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'lang'   => '[a-z][a-z]',
                    ),
                    'defaults' => array(
                        'controller' => 'AuthController',
                        'action'     => 'index',
                        'lang'       => 'en',
                    ),
                ),
            ),
            'user' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/:lang/user[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'lang'   => '[a-z][a-z]',
                    ),
                    'defaults' => array(
                        'controller' => 'UserController',
                        'action'     => 'index',
                        'lang'       => 'en',
                    ),
                ),
            ),
            'admin' => array(
                'type'  => 'segment',
                'options' => array(
                    'route'    => '/:lang/admin[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'lang'   => '[a-z][a-z]',
                    ),
                    'defaults' => array(
                        'controller' => 'AdminController',
                        'action'     => 'index',
                        'lang'       => 'en',
                    ),
                )
            ),
            'imdb' => array(
                'type'  => 'segment',
                'options' => array(
                    'route'    => '/:lang/imdb[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'lang'   => '[a-z][a-z]',
                    ),
                    'defaults' => array(
                        'controller' => 'ImdbController',
                        'action'     => 'index',
                        'lang'       => 'en',
                    ),
                )
            ),
            'search' => array(
                'type'  => 'segment',
                'options' => array(
                    'route'    => '/:lang/search[/:action][/:value]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'value'     => '[0-9]*',
                        'lang'   => '[a-z][a-z]',
                    ),
                    'defaults' => array(
                        'controller' => 'SearchController',
                        'action'     => 'index',
                        'value'      => '0',
                        'lang'       => 'en',
                    ),
                )
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'movies' => __DIR__ . '/../view',
            'zf-table' => __DIR__ .  '/../view/zf-table',
        ),
    ),

    'view_helper_config' => array(
        'flashmessenger' => array(
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        )
    ),

    'view_helpers' => array(
        'invokables'=> array(
            'messageHelper' => 'Movies\View\Helper\MessageHelper',
        )
    ),

    'service_manager' => array(
        'aliases' => array(
            'translator' => 'MvcTranslator',
            'Zend\Authentication\AuthenticationService' => 'AuthService',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../public',
            ),
        ),
    ),
);
?>
