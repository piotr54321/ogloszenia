<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
// Paypal library configuration
// ------------------------------------------------------------------------
/*
 * a.) jeżeli ustawione na TRUE to korzystamy z testowego systemu PayPal
 * b.) adres e-mail osoby która ma konto przedsiębiorcy w systemie PayPal,
 *    na ten adres dokonywane są wpłaty.
 * c.) główna waluta transackji
 */
$config['sandbox'] = TRUE; // a.)
$config['business'] = 'example@example.com'; // a.)
$config['paypal_lib_currency_code'] = 'PLN'; // b.)
$config['paypal_lib_button_path'] = 'assets/images/';
$config['paypal_lib_ipn_log'] = TRUE;
$config['paypal_lib_ipn_log_file'] = BASEPATH . 'logs/paypal_ipn.log';
