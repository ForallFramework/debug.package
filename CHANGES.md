#### [Version 0.1.2 Beta](https://github.com/ForallFramework/events.package/tree/0.1.2-beta)

* Removed the old main.php, and created an init.php which works with the new core package.
* Changed some code to work with the new core package API.
* Made it so specifically suppressed error types now work, instead of just boolean.

#### [Version 0.1.1 Beta](https://github.com/ForallFramework/events.package/tree/0.1.1-beta)
_20-Aug-2013_

* Fixed: Missing comma's in friendlyErrorCode map array.

#### [Version 0.1.0 Beta](https://github.com/ForallFramework/events.package/tree/0.1.0-beta)

* Now uses the forall.loader package for initialization.
* Added new methods in Debug:
  - `type()` - Get the type of a variable.
  - `friendlyErrorCode()` - Convert an error code to a string.
  - `formatBacktrace()` - Enhances a debug back-trace array.
* Added new functions in the global scope:
  - `type()` - Get the type of a variable.
  - `uctype()` - Get the type of a variable with its first letter in upper case.
* Added some generic exceptions:
  - `Exception` - The lowest level of exception.
  - `ErrorException` - Exception that PHP error can convert to.
  - `InvalidArgumentException` - Extends Exception.
* Fixed many small bugs:
  - Removed hard-dependency to Monolog.
  - Implemented Singleton methods in Debug.
  - Removed references to undefined variables.
  - A fatal error while handling a fatal error no longer results in a loop.
  - Removed dependency to the yet non-existent package "output".
  - Fixed invalidly named class.
  - Fixed `catch`-blocks without explicit exception type.

#### [Version 0.0.3 Alpha](https://github.com/ForallFramework/events.package/tree/0.0.3-alpha)
_17-Aug-2013_

* Added autoload clause to composer.json.

#### [Version 0.0.2 Alpha](https://github.com/ForallFramework/events.package/tree/0.0.2-alpha)
_17-Aug-2013_

* Removed trailing comma in composer.json

#### [Version 0.0.1 Alpha](https://github.com/ForallFramework/events.package/tree/0.0.1-alpha)
_15-Aug-2013_

* First version.
