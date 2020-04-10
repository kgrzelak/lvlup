# LVLUP Payment Library
lvlup-payment PHP class
https://www.lvlup.pro/

# Installation
Run `composer require` to download dependencies.
```
composer require kgrzelak/lvlup-payment
```

That's all!.

# Example

## Initialization

```php
<?php

use \kgrzelak\lvlup\Payments;

require_once('vendor/autoload.php');

$lvlup = new Payments('api_key_from_lvlup_panel');
```

## Generating trasnsaction
```php
$lvlup->setPaymentDetails('12.00', 'https://example.com', 'https://example.com');
//Transaction url
echo $lvlup->generateTransaction();
```

## Transaction info
```php
$lvlup->getTransactionInfo('transaction_id'); // boolean (paid or not paid :P)
```

## Transactions list
```php
$lvlup->getPayments(); //array
```
