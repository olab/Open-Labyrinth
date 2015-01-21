# Configuration

	return array(
		'route' => 'media/(<uid>/)kohana/<filepath>',
		'regex' => array(
			'filepath' => '.*',
			'uid'      => '.*?',
		),

		'public_dir' => DOCROOT.'media/<uid>/kohana/<filepath>',
		'cache'      => Kohana::$environment === Kohana::PRODUCTION,
		'uid'        => NULL,
	);

## Route & Regex

These are the parameters used to create the route. The defaults will work most of the time but they are configurable so they can be changed when they cause compatibility issues with your application. The regex can be modified if you have different requirements of matching the `uid` and `filepath`.

## Public Directory

This needs to be a public directory where the media files can be written to but must match the URI from the Route. The default values are set to cache media files to `DOCROOT.'media/<uid>/kohana/<filepath>'` because that is what the URI in the route looks like.

## Cache

Simply a boolean value of whether you want the Media module to automatically copy the media to the `public_dir` when it is first accessed.

## UID

This is the cache buster that will be used in the route. I personally use `Kohana::$app_version` which I add to all my applications so that the cache buster is unique every time I deploy the application to production.

This should be unique across the entire project because from a css file you want to be able to use relative paths to images. Your css file would not know where an image is if it had a UID of its own. App versions and repository revisions are good UIDs to use for this reason.