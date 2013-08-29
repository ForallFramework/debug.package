<?php

/**
 * @package forall.debug
 * @author Avaq <aldwin.vlasblom@gmail.com>
 */
namespace forall\debug;

use \forall\debug\exceptions\ErrorException;
use \Exception;

/**
 * The error handling class.
 */
class ErrorHandler
{
  
  /**
   * Handle an error by converting it to an ErrorException object and passing it to the exception handler.
   *
   * @param integer $type The level of the error raised.
   * @param string $message The error message.
   * @param string $file The full name of the file that the error was raised in.
   * @param integer $line The line number the error was raised at.
   * @param array|null $context An array of variables that existed in the scope the error was raised in.
   *
   * @return boolean Whether the error was successfully handled.
   */
  public static function handleError($type, $message, $file, $line, $context = null)
  {
    
    return self::handleException(new ErrorException($type, $message, $file, $line, $context));
    
  }
  
  /**
   * Handle an exception by attempting several strategies.
   *
   * - If error reporting is being suppressed, the exception will generate a warning-level
   *   log entry and furthermore be ignored.
   * - If any custom exception handlers are available through the
   *   `"forall:debug:exception"`-event, they will be allowed to handle the exception.
   *   * If no handlers are available, the exception will be handled by its message being
   *     sent to the system output channel.
   *   * If an exception is raised during this process, this new exception will be handled
   *     by its message being sent to the system output channel.
   * - If an exception is raised during any of the above, this new exception is handled by
   *   its message being sent straight to PHP's default output channel.
   *
   * @param Exception $e The exception to handle.
   *
   * @return boolean Whether the exception was successfully handled.
   */
  public static function handleException(Exception $e)
  {
    
    //The normal way of handling an exception relies on stable systems.
    try
    {
      
      //Get the system logger.
      $core = forall('core.core');
      $debug = forall('debug.debugger');
      $log = $core->getSystemLogger();
      
      //Detect if this is a suppressed ErrorException.
      if($e instanceof ErrorException && !(error_reporting() & $e->getCode()))
      {
        
        //Generate a warning-level log entry. Behaviour might be intended.
        $log->warning(sprintf(
          'A suppressed %s was caught: "%s" in %s at %s.',
          get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()
        ));
        
        //Do nothing else.
        return true;
        
      }
      
      //Get the event dispatcher and system output channel.
      $dispatch = forall('events.dispatcher');
      #TEMP: Disabled - $outputter = $core->getSystemOutputter();
      
      //Generate an error-level log entry; this is an uncaught exception.
      $log->error(sprintf('A(n) %s occurred.', get_class($e)));
      
      //No exception handlers? We've got some simple logic of our own to deal with this.
      if(empty($dispatch->listeners('forall:debug:exception', null, true)))
      {
        
        $msg = ''
        . $e->getMessage()
        . "\n\n"
        . wrap($debug->formatBacktrace($e->getTrace()))->pluck('action')->join("\n");
        
        #TEMP: Disabled - $outputter->addOutput($msg);
        echo $msg;
        
      }
      
      //Try to let any custom exception handlers handle it.
      try{
        $dispatch->trigger('forall:debug:exception:'.wrap($e)->class()->replace('\\', ':'), $e);
      }
      
      //There was a new exception.
      catch(Exception $e2)
      {
        
        //Log this failure.
        $log->error(sprintf(
          'A(n) %s occurred while handling %s through the exception event.',
          get_class($e2), get_class($e)
        ));
        
        //This exception goes straight to an output channel. We can't trust the handlers.
        #TEMP: Disabled - $outputter->addOutput($e->getMessage());
        echo $e->getMessage();
        
      }
      
    }
    
    //The exception handling systems were not reliable. This is of greater concern.
    catch(Exception $e2)
    {
      
      //Try to do the very least.
      try{
      
        //Just use echo. We don't know if the outputting system is reliable.
        echo $e2->getMessage();
        
        //Add a log entry.
        $core->getSystemLogger()->critical(sprintf(
          'A(n) %s occurred while trying to handle %s.', get_class($e2), get_class($e)
        ));
        
      }catch(Exception $e){}
      
      //Break.
      exit;
      
    }
    
    //Successfully handled the exception.
    return true;
    
  }
  
  /**
   * Check if a fatal error has occurred before this method call and handle it using the error handler.
   *
   * @return void
   */
  public static function handleFatal()
  {
    
    //Get the last error and back-trace info.
    $e = error_get_last();
    
    //If the error occurred within this file, we can no longer trust the error handlers.
    if($e['file'] === __FILE__){
      printf('Failed to handle an error because of ERROR-%s: %s.', $e['type'], $e['message']);
      return;
    }
    
    //If it's a fatal one, then that's probably the reason for shut down, and we should handle it.
    if($e['type'] == E_ERROR){
      error_reporting(E_ALL);
      self::handleError($e['type'], $e['message'], $e['file'], $e['line']);
    }
    
  }
  
}
