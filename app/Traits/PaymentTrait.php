<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait PaymentTrait
{
    protected function makePaymentRequest($url, $data)
    {
        $response = Http::post($url, $data);

        if ($response->status() != 200) {
            throw new \Exception('Payment failed: ' . $response->json()['message']);
        }
    }
}
