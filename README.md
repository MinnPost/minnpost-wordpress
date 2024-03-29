# MinnPost WordPress

This repository is [MinnPost.com](https://www.minnpost.com) in WordPress. It runs live on [WordPress VIP](https://vip.wordpress.com/). This repository exists to faciliate open sharing of code and other development work, but because of the nature of VIP Go it does not include some required configuration files.

## Dependencies

1. SSH Access
2. Git
3. Composer
    - [wp-cli/wp-cli](https://packagist.org/packages/wp-cli/wp-cli)

## Development setup

You can run this repository as a local WordPress installation. It requires VIP files to work properly. The best document to read about this is the one [hosted by WordPress VIP](https://vip.wordpress.com/documentation/vip-go/local-vip-go-development-environment/).

1. Clone this repository.
1. If you have access to a backup of the site's database from the host:
    - Import the database. If the name isn't what you'd like to use locally, rename it.
    - Change the `home` and `siteurl` values from `wp_options` in that case.
1. Create a database.
1. Install WordPress
    - `wp core download`
    - `wp core install`:  `wp core install --url=<url> --title=<site title> --admin_user=<adminuser> --admin_email=<adminemail> --admin_password=<password> --skip-email`
    - If you have access to a backup of the site's database, you can skip `wp core install` and instead import that database. You'll need to change `home` and `siteurl` values from `wp_options` in that case.
    - If you have a newer MySQL, at least as of June of 2020, in order to successfully run a WordPress import you'll need to run this SQL: `set global sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';`. Then be sure to restart MySQL, ex with `brew services restart mysql` if you use Homebrew.
1. Delete the `wp-content` folder if you have access to the private repository for that folder. Clone that repository as `wp-content`. Use `--recursive` to clone the submodules. `git clone --recursive gitrepo.git wp-content`
1. Add the built version of the VIP Go MU plugins. `git clone https://github.com/Automattic/vip-go-mu-plugins-built.git mu-plugins`.
1. Install memcache and memcached.
    - `brew install memcached` 
    - `pecl download memcache`
    - `open memcache-8.0.tgz`
    - `cd memcache-8.0/memcache-8.0`
    - `phpize`
    - `./configure --with-zlib-dir=/usr/local/Cellar/zlib/1.2.11`
    - `make`
    - `sudo make install`
    - In the php.ini file, add `extension="memcache.so"`. If you aren't sure where the correct ini file is, you can run `phpinfo();` and look for the "Loaded Configuration File" value.
    - `valet restart` or other command, if you aren't running Valet. Restarting PHP alone does not seem to work, at least in a Valet environment.
1. Install graphicsmagick.
    - `brew install graphicsmagick`
    - `pecl download gmagick`. You might have to use `pecl install gmagick-2.0.6RC1` (or equivalent version)
    - This should run the full install for Gmagick.
    - `valet restart` or other command, if you aren't running Valet. Restarting PHP alone does not seem to work, at least in a Valet environment.
1. Symlink the `object-cache.php` file into the `wp-content` folder. Use this command (edit the path to your site root if necessary): `ln -s ~/Sites/minnpost-wordpress/wp-content/mu-plugins/drop-ins/object-cache/object-cache.php ~/Sites/minnpost-wordpress/wp-content/`.
1. Update `wp-config.php`:
```php
define( 'VIP_GO_ENV', 'local' );

/** Development */
define( 'SAVEQUERIES', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
define( 'WP_DEBUG', true );
define( 'JETPACK_STAGING_MODE', true );
define( 'JETPACK_DEV_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

// Load early dependencies
if ( file_exists( __DIR__ . '/wp-content/mu-plugins/000-pre-vip-config/requires.php' ) ) {
	require_once __DIR__ . '/wp-content/mu-plugins/000-pre-vip-config/requires.php';
}
// // Loading the VIP config file
if ( file_exists( __DIR__ . '/wp-content/vip-config/vip-config.php' ) ) {
	require_once __DIR__ . '/wp-content/vip-config/vip-config.php';
}

// Defining constant settings for file permissions and auto-updates
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
```
1. Install client-mu-plugins that are only for local use. These are already in the `.gitignore` file to make sure they aren't used remotely.
    - An [MU Autoloader plugin](https://gist.github.com/acki/a7132dfdb97da3404259ee802cce2bd7).
    - [Coral Remote Images](https://wordpress.org/plugins/coral-remote-images/) (optional, used for displaying remote images). Add `define( 'CORAL_REMOTEIMAGES_PROD_URL', 'https://www.minnpost.com' );` to the `wp-config.php` file.
    - [MailHog for WordPress](https://wordpress.org/plugins/wp-mailhog-smtp/) if you use MailHog locally.
    - [Environment Files](https://gist.github.com/jonathanstegall/905df73aed3a7d1b9255167eb7979509)
1. Install Elasticsearch and enable its use in the local environment by following [these instructions](https://gist.github.com/jonathanstegall/aef855c21156eaf526aadef27d8cfb99).

## Maintenance

To update the VIP MU plugins, regularly run these commands:

- `cd wp-content/mu-plugins/`
- `git pull origin master`

## Deployment

Develop new features in their own branch, or in the `develop` branch. Pushing to `develop` will deploy to https://dev.minnpost.com, `preprod` will deploy to https://stage.minnpost.com.

To deploy new features, create a pull request in the private repository. The `master` branch, when it has new pull requests, will allow the VIP code bot to check the changes for problems.

The bot can take some time before it starts running. Once it returns, if there are no problems, merge the pull request.
