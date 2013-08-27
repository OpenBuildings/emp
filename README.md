# Emp

This is an api for accessing eMerchantPay services

## A quick example

```php
use OpenBuildings\Emp\Api;

$api = new Api('https://my.emerchantpay.com', CLIENT_ID, API_KEY);

$response = $api->request('/service/order/submit', array(
	'payment_type' => 'creditcard',
	'test_transaction' => 1,
));

print_r($response);
```

## Using static instance

You usually want to have this api configured once, then used everywhere with that config. To do that, you can use the configure / instance static methods

```php
use OpenBuildings\Emp\Api;

Api::configure('https://my.emerchantpay.com', CLIENT_ID, API_KEY);

// ...

Api::instance();
```

## Threat matrix

To enable threat matrix security you need to use Threatmatrix class. First in the page where the form of the request is displayed (right before the api request) you'll need to have this:

```php
use OpenBuildings\Emp\Api;

$thm = new Threatmatrix(ORG_ID, CLIENT_ID);

// Save the current Threatmatrix in the session
Session::set('thm', $thm);

// Use this somewhere in your views
echo $thm->tracking_code();
```

After that, to perform an api request, add thm to the api instance, like this

```php
use OpenBuildings\Emp\Api;

$api = new Api('https://my.emerchantpay.com', CLIENT_ID, API_KEY);
$thm = Session::get('thm');

$response = $api
	->threatmatrix($thm)
	->request('/service/order/submit', array(
		'payment_type' => 'creditcard',
		'test_transaction' => 1,
	));

print_r($response);
```

## License

Copyright (c) 2012-2013, OpenBuildings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.

