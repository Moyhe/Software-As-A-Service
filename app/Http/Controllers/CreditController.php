<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeatureResource;
use App\Http\Resources\PackageResource;
use App\Models\Feature;
use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    public function index()
    {

        $packages = Package::all();
        $features = Feature::query()->where('active', true)->get();

        return inertia('Credit/Index', [
            'features' => FeatureResource::collection($features),
            'packages' => PackageResource::collection($packages),
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    public function buyCredit(Package $package)
    {

        $strip = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        $checkout_session = $strip->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'USD',
                        'unit_amount' => $package->price * 100,
                        'product_data' => [
                            'name' => $package->name . '-' . $package->credits . 'credits'
                        ],
                    ],
                    'quantity' => 1
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('credit.success', [], true),
            'cancel_url' => route('credit.cancel', [], true)
        ]);

        Transaction::create([
            'status' => 'pending',
            'price' => $package->price,
            'credits' => $package->credits,
            'session_id' => $checkout_session->id,
            'user_id' => Auth::id(),
            'package_id' => $package->id
        ]);

        return redirect($checkout_session->url);
    }

    public function success()
    {
        return to_route('credit.index')->with('success', 'you successfully bought new creidts');
    }

    public function cancel()
    {
        return to_route('credit.index')->with('error', 'there was an error in payment process, please try again');
    }

    public function webhook()
    {

        $endpoint_secret = env('STRIPE_WEBHOOK_KEY');
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        $event = null;

        try {
            $event = \Stripe\webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            return response('', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session =  $event->data->object;

                $transaction = Transaction::query()->where('session_id', $session->id)->first();

                if ($transaction && $transaction->status === 'pending') {
                    $transaction->status = 'paid';
                    $transaction->save();

                    $transaction->user->available_credits += $transaction->credits;
                    $transaction->user->save();
                }
                break;
            default:
                echo 'recived unknown type' . $event->type;
                break;
        }

        return response('');
    }
}
