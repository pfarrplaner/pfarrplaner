<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Http\Controllers;


use App\Liturgy\Block;
use App\Liturgy\Item;
use App\Service;
use Illuminate\Http\Request;

class LiturgyItemController extends Controller
{

    /**
     * LiturgyItemController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param Block $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Service $service, Block $block)
    {
        $data = $this->validateRequest($request);
        $data['liturgy_block_id'] = $block->id;
        $data['sortable'] = count($block->items);

        $item = Item::create($data);
        if (isset($data['data'])) {
            $item->data = $data['data'];
            $item->checkMarkerReplacementSettings();
            $item->save();
        }
        return redirect()->route('liturgy.editor', ['service' => $service->slug, 'autoFocusBlock' => $block->id, 'autoFocusItem' => $item->id]);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param Block $block
     * @param Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Service $service, Block $block, Item $item)
    {
        $data = $this->validateRequest($request);
        $item->update($data);
        if ($data['data']) {
            $item->data = $data['data'];
            $item->checkMarkerReplacementSettings();
            $item->save();
        }
        return redirect()->route('liturgy.editor', $service->slug);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param Block $block
     * @param Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Service $service, Block $block, Item $item)
    {
        $item->delete();
        return redirect()->route('liturgy.editor', $service->slug);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param Block $block
     * @param Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function roster(Request $request, Service $service, Block $block, Item $item)
    {
        $data = $item->data;
        $data['responsible'] = $request->all();
        $item->data = $data;
        $item->save();
        return redirect()->route('liturgy.editor', $service->slug);
    }

    /**
     * Validate the request
     * @param Request $request
     * @return array
     */
    protected function validateRequest(Request $request): array
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

}
