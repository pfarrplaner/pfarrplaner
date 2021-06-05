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

namespace App\Integrations\CommuniApp;


use App\City;
use App\Integrations\AbstractIntegration;
use App\Service;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CommuniAppIntegration extends AbstractIntegration
{
    const AUTH_HEADER = 'X-Authorization';
    const COMMUNI_API_URL = 'https://api.communiapp.de';
    const ROUTE_EVENT = '/rest/event';


    /** @var Client */
    protected $client = null;

    /** @var City */
    protected $city = null;

    /**
     * Check if this integration is active for a particular city
     * @param City $city
     * @return bool
     */
    public static function isActive(City $city): bool
    {
        return ($city->communiapp_token != '') && ($city->communiapp_default_group_id !== null);
    }

    /**
     * Get an instance of this integration for a particular city
     * @param City $city
     * @return CommuniAppIntegration
     */
    public static function get(City $city): CommuniAppIntegration
    {
        return new self($city);
    }

    /**
     * CommuniAppIntegration constructor.
     * @param City $city
     */
    public function __construct(City $city)
    {
        $this->city = $city;
        $this->client = new Client(
            [
                'base_uri' => self::COMMUNI_API_URL,
                'headers' => [
                    self::AUTH_HEADER => sprintf(' Bearer %s', $city->communiapp_token),
                    'Content-Type' => 'application/json',
                ],
            ]
        );
    }

    /**
     * Create a new service on CommuniApp
     * @param Service $service
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleServiceCreated(Service $service)
    {
        Log::debug('Creating CommuniApp service #'.$service->id, $this->getServiceArray($service));
        $response = $this->client->post(self::ROUTE_EVENT, ['body' => json_encode($this->getServiceArray($service))]);
        if ($response->getStatusCode() == 200) {
            $service->update(['communiapp_id' => json_decode($response->getBody())->id]);
        }
    }

    public function handleServiceUpdated(Service $service)
    {
        if (!$service->communiapp_id) {
            return $this->handleServiceCreated($service);
        };
        Log::debug('Updating CommuniApp service for #'.$service->id, $this->getServiceArray($service));
        $response = $this->client->put(sprintf('%s/%s', self::ROUTE_EVENT, $service->communiapp_id),
                                       ['body' => json_encode($this->getServiceArray($service))]);
    }

    /**
     * Convert a service to a dataset array for CommuniApp
     * @param Service $service
     * @return array
     */
    protected function getServiceArray(Service $service): array {
        return [
            'dateTime' => $service->dateTime->setTimezone('Europe/Berlin')->format('Y-m-d H:i:s'),
            'isOfficial' => true,
            'group' => $this->city->communiapp_default_group_id,
            'title' => $service->titleText(false, true),
            'location' => $service->locationText(),
            'description' => $service->broadcast_description,
        ];
    }

    /**
     * Delete a service from CommuniApp
     * @param Service $service
     */
    public function handleserviceDeleted(Service $service)
    {
        if (!$service->communiapp_id) return;
        Log::debug('Deleting CommuniApp service for #'.$service->id, $this->getServiceArray($service));
        $response = $this->client->delete(sprintf('%s/%s', self::ROUTE_EVENT, $service->communiapp_id));
    }

}
