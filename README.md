PHP-Proxy
=========

This script allows cross-domain JavaScript AJAX calls using GET and POST (and propably other methods) to any server by acting as a proxy.

In an example where JavaScript on `www.webhost.com` would like to make an AJAX request to an resource on `api.server.com` (say `http://api.server.com/resource.json`) but cannot because of cross domain security restrictions, the request can be made via the proxy.

The proxy accepts one paramater: `url`, the URL requested. So, place the script on a PHP host and from JavaScript call something like:

    http://www.phphost.com/php-proxy/index.html?url=http://api.server.com/resource.json

By default, CORS is enabled on the script, meaning any domain can call the script. To limit calls to only your host (recommended), comment out the line `enable_cors();` or modify the script as needed.