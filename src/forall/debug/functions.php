<?php

/**
 * @package forall.debug
 * @author Avaq <aldwin.vlasblom@gmail.com>
 */
namespace forall\debug
{
  
  /**
   * An alias for `forall('debug.debugger')->type($var)`.
   * @param mixed $var
   * @return string
   * @see Debug::type() for documentation.
   */
  function type($var)
  {
    
    return forall('debug.debugger')->type($var);
    
  }
  
  /**
   * Calls `forall('debug.debugger')->type($var)` and converts the first letter to upper case.
   * @param mixed $var
   * @return string
   * @see Debug::type() for documentation.
   */
  function uctype($var)
  {
    
    return ucfirst(forall('debug.debugger')->type($var));
    
  }
  
}

//Include global name space for exports.
namespace
{
  
  //Export the "type" function to the global name space.
  if(!function_exists("type")){
    function type($var){
      return \forall\debug\type($var);
    }
  }
  
  //Export the "uctype" function to the global name space.
  if(!function_exists("uctype")){
    function uctype($var){
      return \forall\debug\uctype($var);
    }
  }
  
}
