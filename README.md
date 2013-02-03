php-lpd
=======

PHP LPD (virtual printer in PHP)

* Run `example\server.php` (CLI should be better), the server listens on 127.0.0.1:515 (change that if needed).

You should be good to go, you can either:

* Run `example\client.php` (which works with a lovely class by Mick Sear), change the IP:PORT if needed.

or

* Install a generic LPR text printer and start printing

Data sent to the printer is echoed, and then written to a `example\dump.txt` text file, you can change that in `example\server.php`.

