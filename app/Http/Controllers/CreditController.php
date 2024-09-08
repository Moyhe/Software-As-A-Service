<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeatureResource;
use App\Http\Resources\PackageResource;
use App\Models\Feature;
use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        DB::beginTransaction();

        try {
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
                'success_url' => route('credit.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
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
        } catch (\Exception $e) {
            DB::rollBack();

            Log::critical(__METHOD__ . 'method does not work' . $e->getMessage());
        }

        DB::commit();

        return redirect($checkout_session->url);
    }

    public function success()
    {

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $sessionId = request()->get('session_id');

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            if (!$session) {
                throw new NotFoundHttpException();
            }

            $transaction = Transaction::where('session_id', $session->id)->first();
            if (!$transaction) {
                throw new ModelNotFoundException();
            }
            if ($transaction->status === 'pending') {
                $transaction->status = 'paid';
                $transaction->save();

                $transaction->user->available_credits += $transaction->credits;
                $transaction->user->save();
            }
            return to_route('credit.index')->with('success', 'you successfully bought new creidts');
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }
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

        DB::transaction(function () use ($event) {

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
        });

        return response('');
    }
}
