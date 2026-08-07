<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ServiceOrder;
use App\Support\TelegramNotifier;
use Illuminate\Http\Request;

/**
 * Aloqa formasi va xizmat buyurtmalari — eski `public/send-message.php` o'rniga.
 *
 * Ma'lumot avval bazaga yoziladi (yo'qolmasligi uchun), so'ng Telegram'ga yuboriladi.
 */
class ContactController extends Controller
{
    public function __construct(private readonly TelegramNotifier $telegram) {}

    /** Aloqa sahifasidagi forma. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $message = ContactMessage::create($data + $this->meta($request));

        $this->telegram->send('Yangi xabar — iqbolshoh.uz', [
            'Ism'   => $message->name,
            'Email' => $message->email,
            'Mavzu' => $message->subject,
            'Xabar' => $message->message,
            'IP'    => $message->ip,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Xabaringiz yuborildi. Tez orada bog\'lanaman!',
        ], 201);
    }

    /** Xizmatlar sahifasidagi buyurtma modali. */
    public function storeServiceOrder(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:150'],
            'phone'        => ['nullable', 'string', 'max:50'],
            'message'      => ['nullable', 'string', 'max:5000'],
            'serviceId'    => ['nullable', 'integer', 'exists:services,id'],
            'serviceName'  => ['nullable', 'string', 'max:150'],
            'servicePrice' => ['nullable', 'string', 'max:50'],
        ]);

        $order = ServiceOrder::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'message'       => $data['message'] ?? null,
            'service_id'    => $data['serviceId'] ?? null,
            'service_name'  => $data['serviceName'] ?? null,
            'service_price' => $data['servicePrice'] ?? null,
        ] + $this->meta($request));

        $this->telegram->send('Yangi buyurtma — iqbolshoh.uz', [
            'Ism'     => $order->name,
            'Email'   => $order->email,
            'Telefon' => $order->phone,
            'Xizmat'  => $order->service_name,
            'Narx'    => $order->service_price,
            'Izoh'    => $order->message,
            'IP'      => $order->ip,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buyurtmangiz qabul qilindi. Tez orada bog\'lanaman!',
        ], 201);
    }

    /** @return array<string, string|null> */
    private function meta(Request $request): array
    {
        return [
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ];
    }
}
