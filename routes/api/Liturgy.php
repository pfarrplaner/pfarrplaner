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

use App\Http\Controllers\Api\LiturgicalTextsController;
use App\Http\Controllers\Api\LiturgyController;

// Tree
Route::post('liturgy/tree/save/{service}', [LiturgyController::class, 'saveTreeState'])->name('liturgy.tree.save');
Route::post('liturgy/service/{service}/import/{source}', [LiturgyController::class, 'importToTree'])->name('liturgy.tree.import');

// Block
Route::post('/liturgy/service/{service}/blocks', [LiturgyController::class, 'storeBlock'])->name('liturgy.block.store');
Route::patch('/liturgy/block/{block}', [LiturgyController::class, 'updateBlock'])->name('liturgy.block.update');
Route::delete('/liturgy/block/{block}', [LiturgyController::class, 'destroyBlock'])->name('liturgy.block.destroy');

// Item
Route::post('/liturgy/block/{block}/items', [LiturgyController::class, 'storeItem'])->name('liturgy.item.store');
Route::patch('/liturgy/item/{item}', [LiturgyController::class, 'updateItem'])->name('liturgy.item.update');
Route::delete('/liturgy/item/{item}', [LiturgyController::class, 'destroyItem'])->name('liturgy.item.destroy');
Route::post('/liturgy/item/{item}/assign', [LiturgyController::class, 'assignToItem'])->name('liturgy.item.assign');

// Texts
Route::get('/liturgy/texts/list', [LiturgicalTextsController::class, 'list'])->name('liturgy.text.list');
Route::post('/liturgy/text/import', [LiturgicalTextsController::class, 'importFromWord'])->name('liturgy.text.import');

// Sources
Route::get('/liturgy/sources/{serviceId}', [LiturgyController::class, 'sources'])->name('liturgy.sources');

