<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\Helpers\UnitConversionHelper;

class PaymentController extends Controller
{

  public function process_payment(Request $request){

    \Stripe\Stripe::setApiKey(\Config::get('stripe.test.sk'));

    $amt = $request->get('amt');

    if(!is_numeric($amt)){
      return response()->json(['error' => ['message' => 'Sorry, the amount entered appears to not be a number. Please enter a new amount.']])->status(400);
    } else if($amt < 1) {
      return response()->json(['error' => ['message' => 'Sorry, the amount you entered does not meet the miniumum of $1.00. Please enter a new amount.']])->status(400);
    }

    try {
      $email = $request->get('email');
      $token = $request->get('token');
      
      // TODO: Feature: Could do something with the values returned in cahrge if desired...
      $charge = \Stripe\Charge::create([
        'receipt_email' => $email,
        'source' => $token,
        'amount' => UnitConversionHelper::dollarsToCents($amt), 
        'currency' => 'cad',
        'description' => 'test charge from React Laravel API'
      ]);
      return response()->json(['message' => 'Payment Processed Successfully'])->status(200);

    } catch(\Stripe\Error\Card $e) {
      return response()->json($e)->status($e->getHttpStatus());
    } catch (\Stripe\Error\RateLimit $e) {
      // Too many requests made to the API too quickly
      return response()->json($e)->status($e->getHttpStatus());
    } catch (\Stripe\Error\InvalidRequest $e) {
      // Invalid parameters were supplied to Stripe's API
      return response()->json($e)->status($e->getHttpStatus());
    } catch (\Stripe\Error\Authentication $e) {
      // Authentication with Stripe's API failed
      return response()->json($e)->status($e->getHttpStatus());
    } catch (\Stripe\Error\ApiConnection $e) {
      // Network communication with Stripe failed
      return response()->json($e)->status($e->getHttpStatus());
    } catch (\Stripe\Error\Base $e) {
      // Display a very generic error to the user, and maybe send
      // yourself an email
      return response()->json($e)->status($e->getHttpStatus());
    } catch (Exception $e) {
      // Something else happened, completely unrelated to Stripe
      return response()->json($e)->status(500);
    }
  }

  private function setAmtInDollars($amt) {
    // get the float value of the amount that was entered.
    
  }
}