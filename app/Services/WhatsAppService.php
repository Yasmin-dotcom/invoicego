<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function sendMessage($to, $message)
    {
        $token = env('WHATSAPP_TOKEN');
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');

        $url = "https://graph.facebook.com/v19.0/$phoneNumberId/messages";

        return Http::timeout(10)->withToken($token)->post($url, [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => [
                "body" => $message
            ]
        ]);
    }
}