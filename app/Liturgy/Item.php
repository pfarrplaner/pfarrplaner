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

namespace App\Liturgy;


use App\Liturgy\ItemHelpers\AbstractItemHelper;
use App\Liturgy\ItemHelpers\ItemHelperNotFoundException;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    protected $appends = ['data'];

    protected $table = 'liturgy_items';

    protected $fillable = ['liturgy_block_id', 'title', 'instructions', 'data_type', 'serialized_data', 'sortable'];

    protected const replacement = ['pastors' => 'P', 'organists' => 'O', 'sacristans' => 'M'];

    protected static function boot()
    {
        parent::boot();

        // Order by sortable ASC
        static::addGlobalScope(
            'order',
            function (Builder $builder) {
                $builder->orderBy('sortable', 'asc');
            }
        );
    }


    public function block()
    {
        return $this->belongsTo(Block::class, 'liturgy_block_id');
    }

    public function getDataAttribute()
    {
        return unserialize($this->attributes['serialized_data']) ?: [];
    }

    public function setDataAttribute($data)
    {
        return $this->attributes['serialized_data'] = serialize($data);
    }

    public function recipients() {
        $list = (isset($this->data['responsible']) ? $this->data['responsible'] : []);
        $p = [];
        foreach ($list as $participant) {
            if (is_array($participant)) {
                $type = $participant['type'];
                $id = $participant['name'];
            } else {
                list($type, $id) = explode(':', $participant);
            }
            if ((!$type) || (!$id)) continue;
            if ($type == 'ministry') {
                $id = isset(self::replacement[$id]) ? self::replacement[$id] : $id;
                $p[] = $this->block->service->participantsText($id, true, false);
            } elseif ($type == 'user') {
                $p[] = User::find($id)->fullName(false);
            } else {
                $p[] = $id;
            }
        }
        return array_unique($p);
    }

    /**
     * @return AbstractItemHelper
     * @throws ItemHelperNotFoundException
     */
    public function getHelper(): AbstractItemHelper {
        if (class_exists($class = 'App\\Liturgy\\ItemHelpers\\'.ucfirst($this->data_type).'ItemHelper')) {
            return new $class($this);
        }
        throw new ItemHelperNotFoundException('Item helper class not found for data_type '.$this->data_type);
    }

    /**
     * Check replacement settings for 'liturgic' blocks
     */
    public function checkMarkerReplacementSettings()
    {
        if ($this->data_type != 'liturgic') return;
        $service = $this->block->service;
        $data = $this->data;
        $data['needs_replacement'] ??= '';
        $data['replacement'] ??= '';
        foreach (['funeral' => $service->funerals, 'baptism' => $service->baptisms, 'wedding' => $service->weddings] as $key => $records) {
            if (($data['needs_replacement'] == $key) && ($data['replacement'] == '')) {
                if (count($records)) {
                    $data['replacements'] = $records->first()->id;
                }
            }
        }
        $this->data = $data;
    }

    public function setData($key, $value)
    {
        $this->setDataAttribute(array_merge($this->getDataAttribute(), [$key => $value]));
    }

}
