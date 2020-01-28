# MinnPost WordPress

This repository is [MinnPost.com](https://www.minnpost.com) in WordPress. It runs live on [WordPress.com VIP Go](https://vip.wordpress.com/). This repository exists to faciliate open sharing of code and other development work, but because of the nature of VIP Go it does not include some required configuration files.

## Dependencies

1. SSH Access
2. Git
3. Composer
    - [wp-cli/wp-cli](https://packagist.org/packages/wp-cli/wp-cli)

## Development setup

You can run this repository as a local WordPress installation. It requires VIP files to work properly. The best document to read about this is the one [hosted by VIP Go](https://vip.wordpress.com/documentation/vip-go/local-vip-go-development-environment/).

1. Clone this repository.
2. Create a database.
3. Install WordPress
    - `wp core download`
    - `wp core install`:  `wp core install --url=<url> --title=<site title> --admin_user=<adminuser> --admin_email=<adminemail> --admin_password=<password> --skip-email`
4. Delete the `wp-content` folder if you have access to the private repository for that folder. Clone that repository as `wp-content`. Use `--recursive` to clone the submodules. `git clone --recursive gitrepo.git wp-content`
5. Add the VIP Go MU plugins. `git clone git@github.com:Automattic/vip-go-mu-plugins.git --recursive wp-content/mu-plugins/`
6. Install memcache and memcached.
    - `brew install memcached` 
    - `pecl download memcache`
    - `open memcache-4.0.5.2.tgz`
    - `cd memcache-4.0.5.2/memcache-4.0.5.2`
    - `phpize`
    - `./configure --with-zlib-dir=/usr/local/Cellar/zlib/1.2.11`
    - `make`
    - `sudo make install`
    - `valet restart` or other command, if you aren't running Valet.
7. Symlink the `object-cache.php` file into the `wp-content` folder. Use this command (edit the path to your site root if necessary): `ln -s ~/Sites/minnpost-wordpress/wp-content/mu-plugins/drop-ins/object-cache/object-cache.php ~/Sites/minnpost-wordpress/wp-content/`.
8. Update `wp-config.php`:
```php
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
if ( file_exists( __DIR__ . '/wp-content/vip-config/vip-config.php' ) ) {
    require_once( __DIR__ . '/wp-content/vip-config/vip-config.php' );
}
```

## Development

Develop new features in their own branch, or in the `develop` branch. Pushing to `develop` will deploy to https://dev.minnpost.com, `preprod` will deploy to https://stage.minnpost.com.

To deploy new features, create a pull request in the private repository. The `master` branch, when it has new pull requests, will allow the VIP code bot to check the changes for problems.

The bot can take some time before it starts running. Once it returns, if there are no problems, merge the pull request.
