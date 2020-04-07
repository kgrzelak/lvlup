<?php
/*
* Kwota
*/
$lvlup->set_payment('amount', '20.00');
/*
* Url powrotu przeglądarki klienta po płatności
*/
$lvlup->set_payment('redirectUrl', '');
/*
* Webhook, w trakcie
*/
$lvlup->set_payment('webhookUrl', '');
if (!$lvlup->transaction_generate()) {
	/*
	* Błąd podczas generowania
	*/
	exit('ojoj');
}
/*
* Link do przekierowania użytkownika
*/
echo $lvlup->transaction_redirect();
/*
* Id transakcji
*/
echo $lvlup->transaction_id();