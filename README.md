## COM Port to WebService Proxy Tools

These are some very simple tools to proxy data from a serial port on windows (COM) to a WebSocket so that
a html page can read this data and act on them.

The project is written in PHP and consists of two separate programs:

- `com2ip` - connects to a COM port and forwards the data to a local TCP port
- `ip2web` - Acts as the server.  Establishes listening ports for the raw TCP port and a WebSocket server.  This should be started first before anything else.

# Installation


You need to have php installed (preferrably 7.0+) and composer installed.  Then run `composer install` to download all the dependencies.

You will also need the Direct IO php extension installed.  Download it from [the pecl site](https://pecl.php.net/package/dio/0.1.0/windows)
