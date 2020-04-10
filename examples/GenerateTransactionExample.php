<?php

require_once('vendor/autoload.php');

use \kgrzelak\lvlup\Payments;

$lvlup = new Payments('TOKEN_API');
$lvlup->setPaymentDetails('21.00', 'https://example.com', 'https://example.com');

var_dump($lvlup->generateTransaction()); // transaction url
