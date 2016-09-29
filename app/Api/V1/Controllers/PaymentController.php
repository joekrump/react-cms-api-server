<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use App\Helpers\UnitConversionHelper;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

class PaymentController extends Controller
{
  public function __construct(){
    \Stripe\Stripe::setApiKey(\Config::get('stripe.test.sk'));
  }
  
  public function process_payment(Request $request){

    $validator = Validator::make($request->only(['fname', 'lname', 'email', 'amt']), [
      'email' => 'required|email',
      'fname' => 'required|min:2',
      'lname' => 'required|min:2',
      'amt'   => 'required|numeric|min:5'
    ]);


    if ($validator->fails()) {
      throw new \Dingo\Api\Exception\StoreResourceFailedException('Could not process payment.', $validator->errors());
    }

    try {
      // DEBUGGING TIP: NOTE THAT IF AN INVALID PARAM IS PASSED TO THE CHARGE METHOD (ie. an invalid token or param name) THIS METHOD WILL SIMPLY RETURN 200 and the charge request is silently ignored. 
      // 
      $token = $request->get('token');
      
      // TODO: Feature: Could do something with the values returned in cahrge if desired...
      //       perhaps store stripe customer id with user.
      $charge = \Stripe\Charge::create([
        'receipt_email' => $request->get('email'),
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
}