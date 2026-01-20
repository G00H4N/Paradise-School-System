<?php

namespace App\Services;

class PaymentGateway
{
    public static function initiatePayment($amount, $orderId)
    {
        // JazzCash / EasyPaisa Logic here
        return "https://sandbox.jazzcash.com.pk/pay?order=$orderId&amt=$amount";
    }
}