lvlup-payment PHP class
https://www.lvlup.pro/

Can be installed by Composer.

```
composer require kgrzelak/lvlup
```

Example

Base
```php
<?php
use \kgrzelak\lvlup\Payments;
require_once('vendor/autoload.php');

$lvlup = new Payments('api_key_from_lvlup_panel');
```

Generating trasnsaction
```php
$lvlup->set_payment('amount', '24.00');
$lvlup->set_payment('redirectUrl', '');
$lvlup->set_payment('webhookUrl', '');
if (!$lvlup->transaction_generate()) {
	echo 'ojoj';
	exit();
}
echo 'płać i płacz ';
//Transaction url
echo $lvlup->transaction_redirect();
```

Transaction info
```php
$lvlup->transaction_info('transaction_id'); //bolean
```

Transactions list
```php
$lvlup->payments_get(); //array
```
