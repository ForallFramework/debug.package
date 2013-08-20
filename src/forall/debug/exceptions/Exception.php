<?php

/**
 * @package forall.debug
 * @author Avaq <aldwin.vlasblom@gmail.com>
 */
namespace forall\debug\exceptions;

use \Exception as NativeException;

/**
 * The exception class.
 */
class Exception extends NativeException
{
  
  /**
   * Instantiates the exception class.
   *
   * @param Exception $previous Set the previous exception. Can be omitted in which case it defaults to null.
   * @param string $messageFormat An error message with place holders for `sprintf`.
   * @param mixed $input The first argument for `sprintf`.
   * @param mixed ... The above argument repeats indefinitely.
   */
  public function __construct()
  {
    
    //Get arguments.
    $args = func_get_args();
    
    //Set the previous exception to given or null.
    $previous = ($args[0] instanceof self) ? array_shift($args) : null;
    
    //If there are no further arguments, we have no message.
    if(empty($args)){
      $message = 'No error message.';
    }
    
    //If we have a message, run it through sprintf.
    else{
      $message = call_user_func_array('sprintf', $args);
    }
    
    //Construct the Exception using the native constructor.
    parent::__construct($message, 0, $previous);
    
  }
  
}
