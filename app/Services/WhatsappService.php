<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    public static function sendNotification($targetPhone, $message)
    {
        // Format nomor HP agar diawali dengan 62 (contoh: 08123456789 -> 628123456789)
        $formattedPhone = preg_replace('/^0/', '62', trim($targetPhone));

        $token = env('FONNTE_TOKEN', ''); // Token API dari Fonnte

        if (empty($token)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $formattedPhone,
            'message' => $message,
        ]);

        return $response->successful();
    }
}