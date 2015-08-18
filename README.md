# Omnipay: Pagar.Me

**Pagar.Me gateway for the Omnipay PHP payment processing library**

[![Build Status](https://img.shields.io/travis/descubraomundo/omnipay-pagarme/master.svg?style=flat-square)](https://travis-ci.org/descubraomundo/omnipay-pagarme) [![Code Climate](https://codeclimate.com/github/descubraomundo/omnipay-pagarme/badges/gpa.svg)](https://codeclimate.com/github/descubraomundo/omnipay-pagarme)  [![Test Coverage](https://codeclimate.com/github/descubraomundo/omnipay-pagarme/badges/coverage.svg)](https://codeclimate.com/github/descubraomundo/omnipay-pagarme/coverage)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/descubraomundo/omnipay-pagarme.svg?style=flat-square)](https://packagist.org/packages/descubraomundo/omnipay-pagarme)
[![Total Downloads](https://img.shields.io/packagist/dt/descubraomundo/omnipay-pagarme.svg?style=flat-square)](https://packagist.org/packages/descubraomundo/omnipay-pagarme)

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)


[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Pagar.Me support for Omnipay.

## Install

Via Composer

``` bash
$ composer require descubraomundo/omnipay-pagarme
```

## Basic Usage

The following gateways are provided by this package:

 * [Pagar.Me](https://pagar.me/)

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

### Example
``` php
// Create a gateway for the Pagarme Gateway
  // (routes to GatewayFactory::create)
  $gateway = Omnipay::create('Pagarme');

  // Initialise the gateway
  $gateway->initialize(array(
      'apiKey' => 'MyApiKey',
  ));

  // Create a credit card object
  // This card can be used for testing.
  $card = new CreditCard(array(
              'firstName'    => 'Example',
              'lastName'     => 'Customer',
              'number'       => '4242424242424242',
              'expiryMonth'  => '01',
              'expiryYear'   => '2020',
              'cvv'          => '123',
              'email'        => 'customer@example.com',
              'address1'     => 'Street name, Street number, Complementary',
              'address2'     => 'Neighborhood',
              'postcode'     => '05443100',
              'phone'        => '19 3242 8855',
  ));

  // Do an authorize transaction on the gateway
  $transaction = $gateway->authorize(array(
      'amount'           => '10.00',
      'soft_descriptor'  => 'test',
      'payment_method'   => 'credit_card',
      'card'             => $card,
      'metadata'         => array(
                                'product_id' => 'ID1111',
                                'invoice_id' => 'IV2222',
                            ),
  ));
  $response = $transaction->send();
  if ($response->isSuccessful()) {
      echo "Authorize transaction was successful!\n";
      $sale_id = $response->getTransactionReference();
      $customer_id = $response->getCustomerReference();
      $card_id = $response->getCardReference();
      echo "Transaction reference = " . $sale_id . "\n";
  }
```
## Docs
Read the full Classes Documentation [here](http://descubraomundo.github.io/omnipay-pagarme)

## Test Mode

Pagar.Me accounts have test-mode API keys as well as live-mode API keys. 
Data created with test-mode credentials will never hit the credit card networks
and will never cost anyone money.

Unlike some gateways, there is no test mode endpoint separate to the live mode endpoint, the
Pagar.Me API endpoint is the same for test and for live. 

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/descubraomundo/omnipay-pagarme/issues),
or better yet, fork the library and submit a pull request.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
