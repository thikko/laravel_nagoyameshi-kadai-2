<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;

class TermController extends Controller
{
    // 利用規約ページ
    public function index()
    {
        $term = Term::first();

        return view('terms.index', compact('term'));
    }
}
