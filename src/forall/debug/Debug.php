<?php

/**
 * @package forall.debug
 * @author Avaq <aldwin.vlasblom@gmail.com>
 */
namespace forall\debug;

use \forall\core\core\AbstractCore;

/**
 * The debug class.
 */
class Debug extends AbstractCore
{
  
  /**
   * Sets up error handlers and configuration for proper debugging.
   */
  public function init()
  {
    
    //Set our descriptor.
    $d = $core->createPackageDescriptor('loader');
    $this->setDescriptor($d);
    
    //If error handling is disabled, we've got nothing else to do here.
    if($d->settings['handleErrors'] === false){
      return;
    }
    
    //Register error handlers.
    register_shutdown_function(['ErrorHandler', 'handleFatal']);
    set_error_handler(['ErrorHandler', 'handleError']);
    set_exception_handler(['ErrorHandler', 'handleException']);
    
    //Configure PHP.
    error_reporting(E_ALL);
    ini_set('display_errors', 'off');
    
  }
  
}
