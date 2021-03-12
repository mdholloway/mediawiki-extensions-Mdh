# Extension:Mdh

Personal MediaWiki extension for odds and ends that probably aren't destined for official release.

Featuring:
* **ApiQueryApiModules**: A [Meta](https://www.mediawiki.org/wiki/API:Meta) API module providing data about registered API modules. How meta!

## Installation

Download to the `extensions` folder and add `wfLoadExtension( 'Mdh' );` to [LocalSettings.php](https://www.mediawiki.org/wiki/Manual:LocalSettings.php).

## Testing

1. install nodejs, npm, and PHP composer
2. change to the extension's directory
3. `npm install`
4. `composer install`

Once set up, running `npm test` and `composer test` will run automated code checks.
