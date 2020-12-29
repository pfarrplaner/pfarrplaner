<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sortedCities = $this->getSortedCities()->pluck('id');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'cities' => $this->cities->pluck('id'),
            'sortedCities' => $this->sortedCities,
            'writableCities' => $this->writableCities->pluck('id'),
            'hiddenCities' => $this->cities->reject(function($item) use ($sortedCities) {
                return $sortedCities->contains($item->id);
            }),
        ];
    }
}
