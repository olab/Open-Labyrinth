# Usage

There isn't much in this module for you to use. The module provides a route which you can use to generate URIs like this:

	// Generates something like "/media/v1.2.7/css/styles.css"
	Route::get('media')->uri('css/styles.css');

The module also provides a simple `Media` class with a `url()` and `uri()` method as shortcuts to their equivalents in the route class. This makes generating links as simple as this:

	// Uses reverse routing automatically
	Media::url('css/styles.css');
	// Or
	Media::uri('css/styles.css');

[!!] Both the above methods automatically add any configured cache buster to the generated URL/URI.