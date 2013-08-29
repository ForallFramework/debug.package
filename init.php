<?php namespace forall\debug;

//Create the debugger instance.
$debugger = Debug::getInstance();

//Set up error handlers.
$debugger->setup();

//Register the instance with the core.
$core->register('debugger', $debugger);
