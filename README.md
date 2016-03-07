WooCommerce Tests Framework
===========================

This is tests framework, copied from [here](https://github.com/woothemes/woocommerce/tree/master/tests),
that can be used by WooCommerce extensions.

## Writing Tests for WooCommerce Extension

### Initial Setup

Assume you've [PHPUnit](http://phpunit.de/) and [WP-CLI](http://wp-cli.org/) installed.

1. Scaffold plugin tests using WP-CLI:

   ```
   $ wp scaffold plugin-tests woocommerce-bookings
   ````

2. Install WordPress and the WP Unit Test lib using the `install.sh` script. Change to the plugin root directory and type:

   ```
   $ bin/install-wp-tests.sh <db-name> <db-user> <db-password> [db-host]
   ```

   Sample usage:

   ```
   $ tests/bin/install.sh wc_booking_tests root root
   ```

   **Important**: The `<db-name>` database will be created if it doesn't exist and all data will be removed during testing.

### Setup this repo as subtree

This is for use inside WC extension `tests/` that require the WC tests framework. It can be included as a [subtree](https://hpc.uni.lu/blog/2014/understanding-git-subtree/).

### Initial Sub-Tree setup

```
git remote add -f wc-tests-framework git@github.com:Automattic/wc-tests-framework.git
git fetch wc-tests-framework
git subtree add --prefix tests/framework --squash wc-tests-framework/master
```

### Updating the Sub-Tree

```
git fetch wc-tests-framework
git subtree pull --prefix tests/framework wc-tests-frameworks master --squash
```

### Pushing to the Sub-tree

```
git subtree push --prefix tests/framework wc-tests-frameworks master
```

### Running Tests

Simply change to the plugin root directory and type:

    $ phpunit

The tests will execute and you'll be presented with a summary. Code coverage documentation is automatically generated as HTML in the `tmp/coverage` directory.

