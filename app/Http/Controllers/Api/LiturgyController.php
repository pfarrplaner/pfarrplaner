<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Http\Controllers\Api;

use App\Liturgy\Block;
use App\Liturgy\Item;
use App\Service;
use Illuminate\Http\Request;

class LiturgyController extends \App\Http\Controllers\Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @param Service $service
     */
    public function saveTreeState(Request $request, Service $service)
    {
        foreach ($request->get('blocks') as $block) {
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
        $service->refresh();
        $tree = $service->liturgyBlocks;
        return response()->json(compact('tree'));
    }


    public function importToTree(Request $request, Service $service, Service $source)
    {
        $ct = 0;
        foreach ($service->liturgyBlocks as $block) {
            $block->update(['sortable' => ++$ct]);
        }

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
        $service->refresh();
        return response()->json(['tree' => $service->liturgyBlocks]);
    }


    /**
     * Store a new block
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBlock(Request $request, Service $service)
    {
        $data = $this->validateBlockRequest($request);
        $data['service_id'] = $service->id;
        $data['sortable'] = count($service->liturgyBlocks);
        $block = Block::create($data);
        return response()->json($block);
    }

    /**
     * Update a block
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBlock(Request $request, Block $block)
    {
        $block->update($this->validateBlockRequest($request));
        return response()->json($block);
    }

    /**
     * Delete a block
     * @param Block $block
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyBlock(Block $block)
    {
        $block->delete();
        return response()->json();
    }

    /**
     * Store a new item
     * @param Request $request
     * @param Block $block
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeItem(Request $request, Block $block)
    {
        $data = $this->validateItemRequest($request);
        $data['liturgy_block_id'] = $block->id;
        $data['sortable'] = count($block->items);
        if (!isset($data['responsible'])) {
            $data['responsible'] = [];
        }

        $item = Item::create($data);
        if (isset($data['data'])) {
            $item->data = $data['data'];
            $item->performTypeSpecificActionsOnCreate();
            $item->save();
        }
        $item->refresh();
        return response()->json(
            [
                'item' => $item,
                'focusBlock' => $block->id,
                'focusItem' => $item->id
            ]
        );
    }

    /**
     * Assign responsible people to item
     * @param Item $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignToItem(Request $request, Item $item)
    {
        $data = $item->data;
        $data['responsible'] = $request->all();
        unset($data['responsible']['api_token']);
        $item->data = $data;
        $item->save();
        return response()->json($item->data);
    }

    /**
     * Update an item
     * @param Request $request
     * @param Item $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItem(Request $request, Item $item)
    {
        $data = $this->validateItemRequest($request);
        $item->update($data);
        $item->data = $data['data'];
        $item->save();
        $item->refresh();
        return response()->json(compact('item'));
    }

    public function destroyItem(Item $item)
    {
        $item->delete();
        return response()->json();
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function validateItemRequest(Request $request): array
    {
        return $request->validate(
            [
                'title' => 'required|string',
                'instructions' => 'nullable|string',
                'data_type' => 'required|string',
                'serialized_data' => 'nullable',
                'data' => 'nullable',
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function validateBlockRequest(Request $request): array
    {
        return $request->validate(
            [
                'title' => 'required|string',
                'instructions' => 'nullable|string',
                'service_id' => 'int|exists:services,id',
            ]
        );
    }


}
