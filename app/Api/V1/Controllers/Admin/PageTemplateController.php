<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PageTemplate;
use Dingo\Api\Routing\Helpers;
use App\Transformers\PageTemplateTransformer;

class PageTemplateController extends Controller
{
  use Helpers;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    try {
      $pageTemplates = PageTemplate::orderBy('display_name', 'asc')->get(['display_name', 'id']);
      return $this->response->collection($pageTemplates, new PageTemplateTransformer);
    } catch(Exception $e) {
      return $this->response->error('could_note_get_templates', 500);
    }
  }

  public function updateIndex(Request $request) {

  }
}
