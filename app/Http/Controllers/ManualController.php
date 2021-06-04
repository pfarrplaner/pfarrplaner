<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ManualController extends Controller
{
    public function __construct()
    {
    }

    public function page(Request $request, $routeName)
    {
        $contentFile = file_exists(base_path('manual/' . $routeName . '.md'))
            ? base_path('manual/' . $routeName . '.md')
            : base_path('manual/notfound.md');
        $content = file_get_contents($contentFile);

        $title = explode("\n", $content)[0];

        return Inertia::render('Manual/Page', compact('routeName', 'title', 'content'));
    }
}
