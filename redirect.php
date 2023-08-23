<?php
/**
 * Redirect to PlacetoPay
 */

use PlacetoPay\Loggers\PaymentLogger;

try {
    require_once 'helpers.php';

    $pathCMS = getPathCMS('redirect.php');

    require fixPath($pathCMS . '/config/config.inc.php');
    require fixPath($pathCMS . '/init.php');
    require fixPath(sprintf('%s/%2$s/%2$s.php', _PS_MODULE_DIR_, getModuleName()));

    if (!Context::getContext()->customer->isLogged() && !Context::getContext()->customer->is_guest) {
        PaymentLogger::log('Access not allowed', PaymentLogger::WARNING, 17, __FILE__, __LINE__);
        Tools::redirect('authentication.php?back=order.php');
    }

    $cart = Context::getContext()->cart;
    if (!Validate::isLoadedObject($cart)) {
        PaymentLogger::log('Cart not found', PaymentLogger::ERROR, 18, __FILE__, __LINE__);
        die('Cart not found');
    }

    (new PlacetoPayPayment())->redirect($cart);
} catch (Throwable $e) {
    PaymentLogger::log($e->getMessage(), PaymentLogger::ERROR, 999, __FILE__, __LINE__);
    die($e->getMessage());
}
