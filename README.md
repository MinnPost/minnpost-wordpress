# MinnPost WordPress

This repository is MinnPost.com in WordPress. It is configured to run on a single Linux server, deployed by a [Codeship basic](https://codeship.com/features/basic) free plan.

## Dependencies

1. SSH Access
2. Git
3. Composer
    - [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv)
    - [wp-cli/wp-cli](https://packagist.org/packages/wp-cli/wp-cli)

## Dot Env file

On both local and remote servers, this setup uses a `.env` file (with phpdotenv above) to store the WordPress credentials. Don't ever put this into the repository.

Based on our setup, here is the structure:

```
# database settings
DB_NAME={databasename}
DB_USER={databaseuser}
DB_PASSWORD={databasepassword}
DB_HOST={databasehost}
DB_PREFIX=wp_

# authentication keys and salts - get these at https://api.wordpress.org/secret-key/1.1/salt/
AUTH_KEY={authkey}
SECURE_AUTH_KEY={secureauthkey}
LOGGED_IN_KEY={loggedinkey}
NONCE_KEY={noncekey}
AUTH_SALT={authsalt}
SECURE_AUTH_SALT={secureauthsalt}
LOGGED_IN_SALT={loggedinsalt}
NONCE_SALT={noncesalt}

# salesforce settings
OBJECT_SYNC_SF_SALESFORCE_CONSUMER_KEY={salesforceconsumerkey}
OBJECT_SYNC_SF_SALESFORCE_CONSUMER_SECRET={salesforceconsumersecret}
OBJECT_SYNC_SF_SALESFORCE_CALLBACK_URL={salesforcecallbackurl}
OBJECT_SYNC_SF_SALESFORCE_LOGIN_BASE_URL={salesforceloginurl}
OBJECT_SYNC_SF_SALESFORCE_API_VERSION={salesforceapiversion}
OBJECT_SYNC_SF_SALESFORCE_AUTHORIZE_URL_PATH={salesforceauthorizepath}
OBJECT_SYNC_SF_SALESFORCE_TOKEN_URL_PATH={salesforcetokenpath}

# mailchimp settings
FORM_PROCESSOR_MC_MAILCHIMP_API_KEY={mcapikey}

# gravityforms settings
GF_LICENSE_KEY={gravityformslicensekey}

# site url settings - no trailing slash
WP_HOME = {homepageurl}
WP_SITEURL = {siteurl}

# debug modes - always set these to false on production
WP_DEBUG=true
JETPACK_DEV_DEBUG=true
SCRIPT_DEBUG=true

# payment processor stuff
PAYMENT_PROCESSOR_URL={paymentprocessorurl}

```

## Local setup

1. Clone the repo. Use `--recursive` to clone the submodules. `git clone --recursive gitrepo.git`
2. Run `composer install`
3. Create a database
4. Create a `.env` file in the directory above WordPress filling in the settings above. If using Laravel Valet, this is annoying, but works.
5. Install WordPress
    - `wp core download`
    - `wp core install`:  `wp core install --url=<url> --title=<site title> --admin_user=<adminuser> --admin_email=<adminemail> --admin_password=<password> --skip-email`

## Codeship setup

Create a new project, and connect it to the correct Git repository, and give it the correct team/owner settings.

### Tests

Choose PHP as the technology. Include the following commands:

```
# We support all major PHP versions. Please see our documentation for a full list.
# https://documentation.codeship.com/basic/languages-frameworks/php/
#
# By default we use the latest PHP version from the 5.5 release branch.
phpenv local 7.1
# Install extensions via PECL
#pecl install -f memcache
# Install dependencies via Composer
#composer install --no-interaction
composer install --no-dev --prefer-dist --no-interaction
cd wp-content/plugins/form-processor-mailchimp
composer install --no-dev --prefer-dist --no-interaction
cd ../minnpost-membership
composer install --no-dev --prefer-dist --no-interaction
cd ../minnpost-spills-widget
composer install --no-dev --prefer-dist --no-interaction
cd ../object-sync-for-salesforce
composer install --no-dev --prefer-dist --no-interaction
cd ../../themes/minnpost-largo
composer install --no-dev --prefer-dist --no-interaction
cd ~/
```

All of these commands are important, at least for now, because they ensure that our GitHub-hosted plugins - the ones that are not hosted in the WordPress repository, or that we're not using from there - get their required composer libraries.

It'd be nice to have this done some other way, but this works for now.

### Deploy

For each branch on the server, create a Deployment Pipeline using Custom Script. For example, we use develop, stage, and production (master).

Enter this command in each Custom Script field.

```
# deploy develop branch with rsync
rsync -av ~/clone/ {user@server:path-to-public-root/}
```

## Remote server setup

1. In the home directory, create a `.ssh` directory. Create an `authorized_keys` file with the SSH public key, which you can get from the General settings tab for the Project in Codeship.
2. Create a database and virtual host for each site (ex dev, stage, www as subdomains).
3. For each deploy location (ex dev, stage, www), create a `.env` file in the directory above the public root, filling in the settings above.
4. For each deploy location, run these commands to create the basic WordPress structure:
    - `wp core download`
    - `wp core install`:  `wp core install --url=<url> --title=<site title> --admin_user=<adminuser> --admin_email=<adminemail> --admin_password=<password> --skip-email`


## Deploying to server

Pushing to each git branch that exists in Codeship will cause it to rsync its contents to the server after running the `composer install` command above.
