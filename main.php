<?php namespace forall\debug;

use \forall\loader\AbstractLoader;

class Loader extends AbstractLoader
{
  
  /**
   * Return an array of packages that need to be loaded before this one.
   *
   * @return array
   */
  public static function getDependencies()
  {
    
    return ['forall.events'];
    
  }
  
  /**
   * Load the debug package.
   *
   * @return void
   */
  public static function load()
  {
    
    //Get the core.
    $core = forall('core');
    
    //Get the debug instance.
    $debug = Debug::getInstance();

    //Register the instance with the core.
    $core->registerInstance('debug', $debug);

    //Initialize the instance, calling it's `init` method.
    $core->initializeInstance($debug);
    
  }
  
}
