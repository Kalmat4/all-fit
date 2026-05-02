<?php

namespace App\Http\Controllers\ALLFIT;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramMessageController extends Controller
{
    public function index()
    {
        return view('admin.telegram.index');
    }

    public static function sendMessage($chatID, $message)
    {
        $token = config('services.telegram.token');
    
        try {
            $response = Http::withoutVerifying()->get("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatID,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
            
        } catch (Exception $e) {

            Log::error("Ошибка при отправке сообщения в Telegram пользователю {$chatID}: " . $e->getMessage());
        }

        return $response->json();
    }
}
