<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('payment.index', [
            'products' => Product::all(),
        ]);
    }

    public function process(Product $product): RedirectResponse
    {
        $client = new Client();
        $client->setAuth(
            config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key'
        ));

        $payment = $client->createPayment(
            [
                'amount' => [
                    'value' => $product->price,
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => route('payment.index'),
                ],
                'capture' => true,
                'description' => 'Оплата товара: ' . $product->name,
                'metadata' => [
                    'product_id' => $product->id,
                ]
            ],
            uniqid('', true)
        );

        return redirect($payment->confirmation->confirmation_url);
    }

    public function callback()
    {
        $status = request()->input('object.status');

        match ($status) {
            'succeeded' => Log::info('Успешная оплата'),
            'canceled' => Log::info('Отмена платежа'),
        };

        return 1;
    }
}
