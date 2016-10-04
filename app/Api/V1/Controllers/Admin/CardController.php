<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Card;
use Dingo\Api\Routing\Helpers;
use App\Transformers\CardTransformer;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

class CardController extends Controller
{
  use Helpers;

  public function index()
  {
    $cards = Card::orderBy('position')->get();

    return $this->response->collection($cards, new CardTransformer);
  }

  public function updateIndex(Request $request) {
    $nodesArray = $request->get('nodeArray');
    $node;
    if($nodesArray) {
      $numNodes = count($nodesArray);
      // Note: first entry is being skipped
      for($i = 1; $i < $numNodes; $i++) {
        $node = $nodesArray[$i];
        Card::where('id', $node['item_id'])->update(['position' => $i]);
      }
      return $this->response->noContent()->setStatusCode(200);
    } else {
      return $this->response->error('Update Failed, no data received.', 401);
    }
  }

  public function store(Request $request)
  {
    $credentials = $request->only(['front_content', 'back_content', 'template_id']);

    $validator = Validator::make($credentials, [
      'front_content' => 'required|max:1000',
      'back_content'  => 'required|max:1000'
    ]);

    if($validator->fails()) {
      throw new ValidationHttpException($validator->errors());
    }

    $card = new Card($credentials);

    if($card->save())
      return $this->response->item($card, new CardTransformer)->setStatusCode(200);
    else
      return $this->response->error('Could not create Card', 500);
  }

  public function show($id)
  {

    $card = Card::find($id);

    if($card){
      return $this->response->item($card, new CardTransformer)->setStatusCode(200);
    } 
    return  $this->response->errorNotFound('Could not find Card with id=' . $id);
  }

  public function update(Request $request, $id)
  {
    $card = Card::find($id);
    
    if(!$card) {
      throw new NotFoundHttpException;
    }
    // If there were no values passed in the request then return early.
    if(!$request->all()) {
      return $this->response->error('Nothing to update', 400);
    }

    $credentials = $request->only(['front_content', 'back_content', 'template_id']);

    $validator = Validator::make($credentials, [
      'front_content' => 'max:1000',
      'back_content'  => 'max:1000'
    ]);

    if($validator->fails()) {
      throw new ValidationHttpException($validator->errors());
    }

    if($credentials['front_content']){
      $card->front_content = $credentials['front_content'];
    }
    if($credentials['back_content']){
      $card->back_content = $credentials['back_content'];
    }
    if($credentials['template_id']){
      $card->template_id = $credentials['template_id'];
    }

    if($card->save()){
      return $this->response->item($card, new CardTransformer)->setStatusCode(200);
    } else {
      return $this->response->error('Something went wrong. Could not update the card', 500);
    }
  }

  public function destroy($id)
  {
    $card = Card::find($id);

    if($card) {
      if($card->delete())
        return $this->response->noContent()->setStatusCode(200);
      else
        return $this->response->errorBadRequest('Could Note Remove the Card with id=' . $id);
    }
    return $this->response->errorNotFound('Could not Find Card to remove with an id=' . $id);
  }
}
