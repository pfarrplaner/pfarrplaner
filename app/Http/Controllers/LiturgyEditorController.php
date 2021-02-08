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
        $services = [];
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
        $class = 'App\\Liturgy\\LiturgySheets\\' . $key . 'LiturgySheet';
        if (!class_exists($class)) {
            abort(404);
        }

        /** @var AbstractLiturgySheet $sheet */
        $sheet = new $class();
        $sheet->render($service);
    }

    public function sources(Service $service)
    {
        $services = Service::isNotAgenda()->writable()->orderedDesc()->limit(50)->get();
        $agendas = Service::isAgenda()->get();
        return response()->json(compact('services', 'agendas'));
    }

    public function import(Service $service, Service $source)
    {
        $ct = count($service->liturgyBlocks);
        foreach ($source->liturgyBlocks as $sourceBlock) {
            $ct++;
            unset($sourceBlock->id);
            $newBlock = $sourceBlock->replicate();
            $newBlock->sortable = $ct;
            $newBlock->service_id = $service->id;
            $newBlock->save();
            $itemCtr = 0;
            foreach ($sourceBlock->items as $sourceItem) {
                $itemCtr++;
                $newItem = $sourceItem->replicate();
                $newItem->liturgy_block_id = $newBlock->id;
                $newItem->sortable = $itemCtr;
                if ($newItem->data_type == 'liturgic') {
                    foreach (['funeral' => $service->funerals, 'baptism' => $service->baptisms, 'wedding' => $service->weddings] as $key => $collection) {
                        if ($newItem->data['needs_replacement'] == $key) {
                            $newItem->setData('foo', 'bar');
                            if ($collection->count()) {
                                $newItem->setData('replacement', $collection->first()->id);
                            } else {
                                $newItem->setData('replacement', '');
                            }
                        }
                    }
                }
                $newItem->save();
            }
        }
        return redirect()->route('services.liturgy.editor', $service->id);
    }
}
