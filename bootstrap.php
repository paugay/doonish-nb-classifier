<?php  

/**
 * bootstrap
 *
 * This is a class that initialized Zend Framework and all his 
 * components.
 *
 * @version     $Id$
 * @package     doonish
 * @author      Pau Gay <pau.gay@gmail.com>
 */

/**
 * Initialize the script with all the includes and bootstrapping Zend 
 * Framework.
 */
function initialize() 
{
    defineConstants();
    setIncludePaths();

    // include zend framework
    require_once 'Zend/Application.php';  

    // create application, bootstrap, and run
    $application = new Zend_Application(
        APPLICATION_ENV, 
        APPLICATION_PATH . '/configs/application.ini'
    );

    // initialize session
    $session = new Zend_Session_Namespace('Session');
    $session->jobs = true; 

    $application->bootstrap();

}

/**
 * Define the constants of application path, and enviroment of the application
 */
function defineConstants() 
{
    // define path to application directory
    define('APPLICATION_PATH', 
        realpath(dirname(__FILE__) . '/../../application'));

    // define application environment
    define('APPLICATION_ENV', 
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
}

/**
 * Set the enviroment include path of our applications
 */
function setIncludePaths() 
{
    // ensure library/ is on include_path
    set_include_path(
        implode(
            PATH_SEPARATOR, 
            array(
                realpath(APPLICATION_PATH . '/../library'),
                realpath(APPLICATION_PATH . '/../application/models/'),
                get_include_path(),
            )
        )
    );
}

// call to his own initialize function
initialize();
