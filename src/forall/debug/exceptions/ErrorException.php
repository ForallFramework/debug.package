<?php

/**
 * @package forall.debug
 * @author Avaq <aldwin.vlasblom@gmail.com>
 */
namespace forall\debug\exceptions;

use \ErrorException as NativeErrorException;

/**
 * The error exception class.
 */
class ErrorException extends NativeErrorException
{
  
  /**
   * An array of variables that existed in the scope the error was raised in.
   * @var array
   */
  protected $context;
  
  /**
   * Sets all the error data on the new ErrorException object.
   *
   * @param integer $code The level of the error raised.
   * @param string $message The error message.
   * @param string $file The full name of the file that the error was raised in.
   * @param integer $line The line number the error was raised at.
   * @param array $context An array of variables that existed in the scope the error was raised in.
   */
  public function __construct($code, $message, $file, $line, array $context = [])
  {
    
    //Get the type as string.
    $type = forall('debug')->friendlyErrorCode($code);
    $type = ($type ? $type : 'ERROR');
    
    //Create an extended message.
    $message = "$type: $message";
    
    //Construct via the native constructor.
    parent::__construct($message, $code, 0, $file, $line);
    
    //Store the context.
    $this->context = $context;
    
  }
  
  /**
   * Return the context.
   *
   * @return array An array of variables that existed in the scope the error was raised in.
   */
  public function getContext()
  {
    
    return $this->context;
    
  }
  
}
