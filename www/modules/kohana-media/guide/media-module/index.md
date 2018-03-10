# Media Module

The media module takes advantage of Kohana's Cascading Filesystem to serve media files of any kind.  It does not focus on compressing, concatinating, or compiling files.

# Packaged media files with modules

All media files can be served from the Kohana Cascading Filesystem by being palced in a `media` directory. Application media can go in `APPPATH.'media'`, and media for your module can go in `MODPATH.'yourmodule/media'`. The media module will take care of serving the files for you. This makes it possible to distribute fully packaged modules without having the user copy media to their public directory. Simply enable the modules, and all their media becomes available to the application.

# Cached to a public directory

Through a simple configuration file, the Media module allows you to easily cache files to your public directory so that they are not served through PHP. This eliminates the biggest performance issue of serving media with Kohana.

# Automated Cache Busting

If enabled, the Media module will automatically add a cache buster to the URLs it generates to your media. A common approach is to add the application's version to the URL so that the cache gets busted with every deploy of the application. The Media module uses cache busters in the URI to avoid any issues related to using cache busters in query strings. Automatically busting cached media files allows you to cache media requests very heavily because you don't have to worry about the possibility of old media being served after a new copy is deployed.

[!!] The default configuration places the cache buster before the file path, so that if your css is placed in `media/css/styles.css` and your images in `media/images/*`, you can use images in your css without worrying about the cache buster in the URL. Something like `background: url('../images/bg.png')` will link to the image **with** the cache buster in the URL.

# Reverse routing for media

This module also adds a route to generate links to any media file in the filesystem. Using reverse routing allows Kohana to automatically add the cache buster to the generated URI. This allows you to later change the cache busting options without having to alter any of your application's code. If you want to switch the cache buster from using the application's version, to using the commit hash; simply change it in the config, and all your links will be updated.