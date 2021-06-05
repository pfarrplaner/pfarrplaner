<?php

namespace App\Http\Controllers;

use App\Liturgy\Block;
use App\Liturgy\Item;
use App\Liturgy\LiturgySheets\AbstractLiturgySheet;
use App\Liturgy\LiturgySheets\LiturgySheets;
use App\Liturgy\Resources\BlockResourceCollection;
use App\Participant;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LiturgyEditorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['download']);
    }

    public function editor(Request $request, Service $service)
    {
        $service->load('day', 'liturgyBlocks', 'sermon');
        $liturgySheets = LiturgySheets::all();
        $services = [];
        $autoFocusBlock = $request->get('autoFocusBlock', null);
        $autoFocusItem = $request->get('autoFocusItem', null);
        $ministries = $this->getAvailableMinistries();

        return Inertia::render(
            'liturgyEditor',
            compact('service', 'liturgySheets', 'autoFocusBlock', 'autoFocusItem', 'ministries')
        );
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

    /**
     * Download a rendered LiturgySheet
     * @param Request $request
     * @param Service $service
     * @param $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(Request $request, Service $service, $key)
    {
        $class = 'App\\Liturgy\\LiturgySheets\\' . $key . 'LiturgySheet';
        if (!class_exists($class)) {
            abort(404);
        }

        /** @var AbstractLiturgySheet $sheet */
        $sheet = new $class();
        if ((null !== $sheet->getConfigurationPage()) && (!$request->has('config'))) {
            return redirect()->route('services.liturgy.configure', ['service' => $service->id, 'key' => $key]);
        }

        if (null !== $sheet->getConfigurationPage()) {
            $sheet->setConfiguration($request->get('config', []));
        }
        return $sheet->render($service);
    }

    /**
     * Show the configuration page for a LiturgySheet
     * @param Request $request
     * @param Service $service
     * @param $key
     * @return \Inertia\Response
     */
    public function configureLiturgySheet(Request $request, Service $service, $key)
    {
        $class = 'App\\Liturgy\\LiturgySheets\\' . $key . 'LiturgySheet';
        if (!class_exists($class)) {
            abort(404);
        }
        /** @var AbstractLiturgySheet $sheet */
        $sheet = new $class();

        $sheetConfig = [
            'key' => $sheet->getKey(),
            'title' => $sheet->getTitle(),
            'fileTitle' => $sheet->getFileTitle(),
        ];

        $config = $sheet->getConfiguration();

        return Inertia::render($sheet->getConfigurationPage(), compact('service', 'sheetConfig', 'config'));
    }

    public function sources(Service $service)
    {
        $services1 = Service::isNotAgenda()
            ->writable()
            ->whereHas('liturgyBlocks')
            ->limit(50)->get();
        $services2 = Service::isNotAgenda()
            ->userParticipates(Auth::user(), 'P')
            ->whereHas('liturgyBlocks')
            ->get();
        $services3 = $services1->merge($services2)->unique();
        $services = [];
        foreach ($services3 as $service) {
            $services[$service->dateTime->timestamp . $service->id] = $service;
        }
        krsort($services);
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
                    foreach (
                        [
                            'funeral' => $service->funerals,
                            'baptism' => $service->baptisms,
                            'wedding' => $service->weddings
                        ] as $key => $collection
                    ) {
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

    /**
     * @param $reqMinistries
     * @return array
     */
    protected function getAvailableMinistries()
    {
        $ministries = [];
        foreach (Participant::all()->pluck('category')->unique() as $ministry) {
            switch ($ministry) {
                case 'P':
                case 'O':
                case 'M':
                case 'A':
                    break;
                default:
                    $ministries[$ministry] = $ministry;
            }
        }
        return $ministries;
    }


}
