<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\PageTemplate;

class PageTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTemplates = PageTemplate::orderBy('display_name')->get(['id', 'display_name']);

        return ['data' => $pageTemplates];
    }
}
