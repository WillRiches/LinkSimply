# LinkSimp.ly

A URL shortening service

## License

This work is licenced under the GPL-3.0 License.

## Current Features
*   Modular architecture (MVC)
*   Shorten URL (http/https)
*   URL validation
*   Custom URLs
*   URL generation
*   Generated URL collision avoidance
*   Clean theme
*   Link history persistent against PHP session
*   Link deletion (links pertaining to session only)

## Possible Improvements
*   Produce documentation/supporting UML diagrams
*   Separate routing from the controller for greater future flexibility
*   Admin panel to keep track of link clicks/loads (is currently stored in database)
*   Add DMCA compliance (link reporting system)
*   Record errors/general client usage in database for analytics
*   Make error messages more descriptive
*   Support non-ASCII characters in domain names
*   Use a CAPTCHA to prevent automated systems using the service
*   Provide a rate-limited API to allow automated systems to use the service
*   A check to determine if a valid website can be found at the given URL
*   Write unit tests
*	A temporary landing page that can show adverts etc before redirecting
*   Produce a CLI installer
*   Produce a more modern front-end

## Requirements

*   PHP 7.0+
*   MySQL 5.7+
*   Designed to work without need for rewrites so Apache + Nginx compatible out of the box

## Installation instructions

1. Clone the repo
2. Run bower install to download dependencies
2. Create the database from the db.sql file
3. Update your database credentials in conf/db-conf.php
4. Enjoy!
