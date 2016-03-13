WooCommerce Extension Code Test Guide
=====================================

Guide to write tests (unit, functional, and acceptance) for WooCommerce extension.
This guide is written with [VVV](https://github.com/Varying-Vagrant-Vagrants/VVV)
as development environment as it comes with [PHPUnit](http://phpunit.de/) and
[WP-CLI](http://wp-cli.org/).

## Writing Unit and Functional Tests for WooCommerce Extension

### Initial Setup

All commands below are ran inside VVV, which means you need to `vagrant ssh` first
and change directory to your WordPress installation, e.g. `cd /srv/www/wordpress-default/`.

1. Scaffold plugin tests using WP-CLI:

   ```
   $ wp scaffold plugin-tests <your-slug-extension>
   ````

   if succeed, will output:

   ```
   Success: Created test files.
   ```

   and following files will be created:

   ```
   ├── .travis.yml
   ├── bin
   │   └── install-wp-tests.sh
   ├── phpunit.xml.dist
   └── tests
       ├── bootstrap.php
       └── test-sample.php
   ```

2. Install WordPress and the WP Unit Test library using the `bin/install-wp-tests.sh`
   script. Change to the plugin root directory and type:

   ```
   $ bin/install-wp-tests.sh <db-name> <db-user> <db-password> [db-host]
   ```

   Sample usage:

   ```
   $ bin/install-wp-tests.sh wc_example_extension_test root root
   ```

   **Important**: The `wc_example_extension_test` database will be created if it
   doesn't exist and all data will be removed during testing.

### Running Tests

The scaffold command creates `tests/test-sample.php` which contains simple
assertion. Make sure you can run test on that by simply change to the plugin
root directory and type:


```
$ phpunit
```

The tests will execute and you'll be presented with a summary, something like:

```
Installing...
Running as single site... To run multisite, use -c tests/phpunit/multisite.xml
Not running ajax tests. To execute these, use --group ajax.
Not running ms-files tests. To execute these, use --group ms-files.
Not running external-http tests. To execute these, use --group external-http.
PHPUnit 4.0.20 by Sebastian Bergmann.

Configuration read from /srv/www/wordpress-default/wp-content/plugins/wc-example-extension/phpunit.xml.dist

.

Time: 1.22 seconds, Memory: 23.00Mb

OK (1 test, 1 assertion)
```

### Tests Bootstrap

Once you ran initial setup above, `tests/bootstrap` will be generated. Make sure
to adjust following things:

1. Make sure WooCommerce and extension main files are loaded in `tests/bootstrap.php`:

  ~~~php
  function _manually_load_plugin() {
     require dirname( dirname( __FILE__ ) ) . '/your-woocommmerce-extension.php';
     require dirname( dirname( __FILE__ ) ) . '../../woocommerce/woocommerce.php';
  }

  tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );
  ~~~

2. Make sure `woothemes_queue_update` and `is_woocommerce_active` are defined and
  returns true in `tests/bootstrap.php`:

  ~~~php
  function is_woocommerce_active() {
     return true;
  }

  function woothemes_queue_update($file, $file_id, $product_id) {
     return true;
  }
  ~~~

3. If needed, loads helper and factory classes from WooCommerce:

  ~~~php
  $wc_tests_framework_base_dir = dirname( dirname( __FILE__ ) ) . '../../woocommerce/tests/framework/';
  require_once( $wc_tests_framework_base_dir . 'class-wc-mock-session-handler.php' );
  require_once( $wc_tests_framework_base_dir . 'class-wc-unit-test-case.php' );
  require_once( $wc_tests_framework_base_dir . 'helpers/class-wc-helper-product.php'  );
  require_once( $wc_tests_framework_base_dir . 'helpers/class-wc-helper-coupon.php'  );
  require_once( $wc_tests_framework_base_dir . 'helpers/class-wc-helper-fee.php'  );
  require_once( $wc_tests_framework_base_dir . 'helpers/class-wc-helper-shipping.php'  );
  require_once( $wc_tests_framework_base_dir . 'helpers/class-wc-helper-customer.php'  );
  require_once( $wc_tests_framework_base_dir . 'helpers/class-wc-helper-order.php'  );
  require_once( 'class-wc-booking-product-test-helper.php' );
  ~~~


### Writing Unit and Functional Tests

This guide comes with example extension with tests that follow this guide. In addition
to bootstrap mechanism and directory structure above, all test files are put
in `tests/` directory and prefixed with `test-`, though you can adjust that via
`phpunit.xml.dist` or `phpunit.xml` it's preferred if you stick with default
config. The class name in test file doesn't matter, as PHPUnit will scan test files
based on file name, but we agreed to suffix it `_Test`. For example, in `wc-example-extension`
there are two files that worth to test:

```
└── includes
    ├── class-wc-example-extension-price-emoji.php
    └── class-wc-example-extension.php
```

and associated tests files are:

```
└── tests
    ├── test-class-wc-example-extension-price-emoji.php
    └── test-class-wc-example-extension.php
```

The class declaration inside `test-class-wc-example-extension.php` will look like:

~~~php
class WC_Example_Extension_Test extends WP_UnitTestCase {
}
~~~

### Functional Tests

As the plugin naturally depends on WordPress components that talk to DB and file
system, most of the time you'll write functional tests. In that case, you could
write test that assert functionalities of your extension and not necessarily
test each method.

### Unit Tests

If your extension contains class that doesn't depend on WordPress components, like
`class-wc-example-extension-price-emoji.php`, then you can properly unit test that
class. See [`tests/test-class-wc-example-extension-price-emoji.php`](tests/test-class-wc-example-extension-price-emoji.php)
as an example.

#### Mocking HTTP Request

If your extension talks to external service via HTTP and uses `wp_remote_*` functions,
you can use `pre_http_request` filter to mock the response. Lets say you have a method
`get_shipping_rate_from_external_service` that returns either `WP_Error` or string of rate.
To test that you can do something like:

~~~php
public function test_get_shipping_rate_from_external_service() {

    // Success response.
    add_filter( 'pre_http_request', array( $this, 'pre_http_request_response_success' ) );
    $result = $this->obj->get_shipping_rate_from_external_service();
    $this->assertEquals( '10.00', $result );
    remove_filter( 'pre_http_request', array( $this, 'pre_http_request_response_success' ) );

    // Failed response.
    add_filter( 'pre_http_request', array( $this, 'pre_http_request_response_failed' ) );
    $result = $this->obj->get_shipping_rate_from_external_service();
    $this->assertInstanceOf( 'WP_Error', $result );
    $this->assertEquals( 'Unknown country', $result->get_error_message() );
    remove_filter( 'pre_http_request', array( $this, 'pre_http_request_response_failed' ) );

}

protected function pre_http_request_response_success() {
    return array( 'body' => json_encode( array( 'success' => true, 'rate' => '10.00' ) ) );
}

protected function pre_http_request_response_failed() {
    return array( 'body' => json_encode( array( 'success' => false, 'error_message' => 'Unknown country' ) ) );
}
~~~

#### Mocking WordPress API (Functions and Hooks)

Use [wp_mock](https://github.com/10up/wp_mock). Their README.md is well written
and can be used as quickstart.


## Writing Acceptance Tests

TODO

## TODO

* Guide to test WC components like gateway, shipping, etc
* Complete the guide to write acceptance tests
* Guide to integrate with Travis CI
* Maybe move PHPUnit tests under `tests/php` directory to anticipate JS tests
  that will be put under `tests/js`.
