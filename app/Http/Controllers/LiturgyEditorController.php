<?php

namespace App\Http\Controllers;

use App\Liturgy\Block;
use App\Liturgy\Item;
use App\Liturgy\LiturgySheets\AbstractLiturgySheet;
use App\Liturgy\LiturgySheets\LiturgySheets;
use App\Liturgy\Resources\BlockResourceCollection;
use App\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LiturgyEditorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function editor(Service $service)
    {
        $service->load('day', 'liturgyBlocks');
        $liturgySheets = LiturgySheets::all();
        return Inertia::render('liturgyEditor', compact('service', 'liturgySheets'));
    }

    public function save(Request $request, Service $service)
    {
        foreach ($request->all() as $block) {
            Block::find($block['id'])->update(['sortable' => $block['sortable']]);
            foreach ($block['items'] as $item) {
                Item::find($item['id'])->update(
                    [
                        'sortable' => $item['sortable'],
                        'liturgy_block_id' => $block['id'],
                    ]
                );
            }
        }
        return redirect()->back();
    }

    public function download(Request $request, Service $service, $key)
    {
        $class = 'App\\Liturgy\\LiturgySheets\\'.$key.'LiturgySheet';
        if (!class_exists($class)) abort(404);

        /** @var AbstractLiturgySheet $sheet */
        $sheet = new $class();
        $sheet->render($service);
    }
}
