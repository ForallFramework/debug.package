<?php namespace forall\debug;

//Get the debug instance.
$debug = Debug::getInstance();

//Register the instance with the core.
$core->registerInstance('debug', $debug);

//Initialize the instance, calling it's `init` method.
$core->initializeInstance($debug);
