CheapLocalDeals.com
===================

e-commerce gift certificate system using PHP PEAR, geolocation, Memcached, and other various libraries

Developed back in 2008, coding methods and page layout may appear dated. It was released just when mobile user access was just starting to pick up so mobile access is available but likely buggy. PEAR libraries were used for the majority of the system functions with a few others peppered. FPDF was used for PDF gift certificate generation. Integreated payment methods include Authorize.net and Paypal. 

For geolocation IP lookup, a single flat-file text document was used and updated via cron job. I believe that the script was stored within the server itself so it likely no longer exists within this package, but the flat file database is in here somewhere.

Memcache is referenced throughout and will cause the website to fail to load if it is not installed onto your server.

It also includes a web-based administrator panel, an affiliate system, and an XML API.
