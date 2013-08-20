<?php

/**
 * @package forall.debug
 * @author Avaq <aldwin.vlasblom@gmail.com>
 */
namespace forall\debug;

use \forall\core\core\AbstractCore;
use \forall\core\singleton\SingletonTraits;

/**
 * The debug class.
 */
class Debug extends AbstractCore
{
  
  use SingletonTraits;
  
  /**
   * Sets up error handlers and configuration for proper debugging.
   */
  public function init()
  {
    
    //Set our descriptor.
    $d = forall('core')->createPackageDescriptor('debug');
    $this->setDescriptor($d);
    
    //If error handling is disabled, we've got nothing else to do here.
    if($d->settings['handleErrors'] === false){
      return;
    }
    
    //Register error handlers.
    register_shutdown_function(['\\forall\\debug\\ErrorHandler', 'handleFatal']);
    set_error_handler(['\\forall\\debug\\ErrorHandler', 'handleError']);
    set_exception_handler(['\\forall\\debug\\ErrorHandler', 'handleException']);
    
    //Configure PHP.
    error_reporting(E_ALL);
    ini_set('display_errors', 'off');
    
  }
  
  /**
   * Return the given PHP error code as a human-readable string.
   *
   * @param integer $code The error code to translate.
   *
   * @return string|false The human-readable format.
   */
  public function friendlyErrorCode($code)
  {
    
    return array_search($code, [
      'ERROR'             => E_ERROR,
      'WARNING'           => E_WARNING,
      'PARSING ERROR'     => E_PARSE,
      'NOTICE'            => E_NOTICE,
      'CORE ERROR'        => E_CORE_ERROR,
      'CORE WARNING'      => E_CORE_WARNING,
      'COMPILE ERROR'     => E_COMPILE_ERROR,
      'COMPILE WARNING'   => E_COMPILE_WARNING,
      'USER ERROR'        => E_USER_ERROR,
      'USER WARNING'      => E_USER_WARNING,
      'USER NOTICE'       => E_USER_NOTICE,
      'STRICT NOTICE'     => E_STRICT,
      'RECOVERABLE ERROR' => E_RECOVERABLE_ERROR
    ]);
    
  }
  
  /**
   * Returns the type of the variable with object class names.
   *
   * @param mixed $var Anything.
   *
   * @return string The type of variable given.
   */
  public function type($var)
  {
    
    return (is_object($var) ? sprintf('object(%s)', get_class($var)) : gettype($var));
    
  }
  
  /**
   * Reformats an array in the `debug_backtrace` format to be clearer and more consistent.
   *
   * The following changes are made in the back-trace array:
   * * The file contains a relative path from the Forall root instead of OS root.
   * * An "action" field is added, containing the full name of the function and notables.
   * * Magic __call methods are filtered out.
   * * Call forwarding methods are combined with the forwarded call.
   *
   * @param array $backtrace The back-trace data.
   *
   * @return array An array of back-trace entries.
   */
  public function formatBacktrace(array $backtrace)
  {
    
    //Reverse the back-trace so we can work in chronological order.
    $backtrace = array_reverse($backtrace);
    
    //Iterate the entries.
    for($i=0; array_key_exists($i, $backtrace);)
    {
      
      //Get the entry.
      $entry =& $backtrace[$i];
      
      //Skip magic call.
      if(array_key_exists('class', $entry) && $entry['function'] == '__call'){
        $i++;
        continue;
      }
      
      //If there was no file, it was a call in PHP's internal code.
      if(!array_key_exists('file', $entry)){
        $entry['file'] = '<none>';
        $entry['line'] = '?';
        $entry['action'] = '[internal code]';
      }
      
      //If it happened in a file.
      else{
        #TODO: Strip the path up until the system root once possible.
        $entry['file'] = $entry['file'];
        $entry['line'] = array_key_exists('line', $entry) ? $entry['line'] : '?';
      }
      
      //Define some functions that need to be combined with the next entry.
      $combine = [
        'call_user_func',
        'call_user_func_array',
        'call_user_method',
        'call_user_method_array',
        'forward_static_call',
        'forward_static_call_array'
      ];
      
      //Merge the next entry with this one?
      if(in_array($entry['function'], $combine))
      {
        
        $next =& $backtrace[++$i];
        
        $entry['action'] = ''
        . $this->_formatFunction($next)
        . $this->_formatArguments($next['args'])
        . sprintf(' (via %s)', $this->_formatFunction($entry));
        
      }
      
      //No merging.
      else{
        
        $entry['action'] = ''
        . $this->_formatFunction($entry)
        . $this->_formatArguments($entry['args']);
        
      }
      
      //Increment.
      $i++;
      
    }
    
    //Return the formatted result.
    return $backtrace;
    
  }
  
  /**
   * Used by `formatBacktrace` to convert function meta data to string.
   * @param array $meta
   * @return string
   */
  private function _formatFunction(array $meta)
  {
    
    //If the function is a closure.
    if(substr_count($meta['function'], '{closure}') > 0){
      
      return sprintf('{Closure:%s}', (array_key_exists('class', $meta)
        ? $meta['class']
        : '<none>'
      ));
      
    }
    
    //If the function is a method.
    if(array_key_exists('class', $meta)){
      
      //Object method.
      if(array_key_exists('object', $meta)){
        return sprintf('{%s}', wrap($meta['object'])->name()) . $meta['type'] . $meta['function'];
      }
      
      //Static method.
      else{
        return $meta['class'] . $meta['type'] . $meta['function'];
      }
      
    }
    
    //Just return the function name.
    return $meta['function'];
    
  }
  
  /**
   * Used by `formatBacktrace` to convert arguments to string.
   * @param array $args The arguments.
   * @return string
   */
  private function _formatArguments(array $args)
  {
    
    //Wrap the arguments.
    return wrap($args)
    
    //Create a new array with visualized arguments.
    ->map(function($arg){
      return wrap($arg)->visualize(true)->unwrap();
    })
    
    //Join the arguments together to create a comma-separated list.
    ->join(', ')
    
    //Wrap the list in brackets.
    ->prepend('(')->append(')')
    
    //Unwrap the end result.
    ->unwrap();
    
  }
  
}
