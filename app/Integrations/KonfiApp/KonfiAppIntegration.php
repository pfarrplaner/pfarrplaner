<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Integrations\KonfiApp;


use App\City;
use App\Integrations\AbstractIntegration;
use App\Service;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

/**
 * Class KonfiAppIntegration
 * @package App\Integrations\KonfiApp
 */
class KonfiAppIntegration extends AbstractIntegration
{

    /**
     *
     */
    protected const API_URL = 'https://api.konfiapp.de/v2/';

    /**
     * @var string
     */
    protected $apiKey = '';

    /** @var Client */
    protected $client;

    /**
     * KonfiAppIntegration constructor.
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->setApiKey($apiKey);
        $this->setClient(new Client(['base_uri' => self::API_URL]));
    }

    /**
     * Get an instance of this integration for a particular city
     * @param City $city
     * @return KonfiAppIntegration
     */
    public static function get(City $city): KonfiAppIntegration
    {
        return (new self($city->konfiapp_apikey));
    }

    /**
     * Returns true if the integration is active and properly configured to work for a specific city
     *
     * For this integration, this is the case if the konfiapp_apikey is present.
     *
     * @param City $city
     * @return bool
     */
    public static function isActive(City $city): bool
    {
        return ($city->konfiapp_apikey != '');
    }

    /**
     * Get a collection with all defined event types from KonfiApp
     *
     * @return Collection Event types
     * @throws Exception when request status is not 200
     */
    public function listEventTypes()
    {
        return collect($this->requestData('verwaltung/veranstaltungen/list/')->payload->veranstaltungen);
    }

    /**
     * Send a request to the public API for KonfiApp and return the contents of the response's data field
     *
     * @param $requestType
     * @param $path
     * @param array $arguments
     * @return mixed Response data field
     * @throws Exception
     */
    protected function requestData($path, $arguments = [])
    {
        $response = $this->request('POST', $path, $arguments);
        if ($response->getStatusCode() != 200) {
            throw new Exception ('Could not retrieve event types from KonfiApp.');
        }
        return json_decode((string)$response->getBody());
    }

    /**
     * Send a request to the public API for KonfiApp
     *
     * This will automatically add the api key
     *
     * @param $requestType
     * @param $path
     * @param array $arguments
     * @return ResponseInterface
     */
    protected function request($requestType, $path, $arguments = []): ResponseInterface
    {
        $arguments['apikey'] = $this->apiKey;
        return $this->client->request(
            $requestType,
            $path,
            [
                'query' => $arguments,
                'form_params' => $arguments,
            ]
        );
    }

    /**
     * Handle service update
     *
     * This will create a new qr code if the service does not have one yet
     *
     * @param Service $service
     * @throws Exception
     */
    public function handleServiceUpdate(Service $service, $requestedChange)
    {
        if ($service->konfiapp_event_type != '') {
            if ($service->konfiapp_event_qr == '') {
                Log::debug('Updating service #'.$service->id.', no KonfiApp QR set yet.');
                if (!trim($service->time)) {
                    Log::debug('No service time set yet, aborting.');
                    return;
                }
                $code = $this->createQRCode($service);
                Log::debug('Got code '.$code);
                $service->update(['konfiapp_event_qr' => $code]);
                $service->refresh();
                Log::debug('Updated service to code '.$service->konfiapp_event_qr);
            } elseif ($service->konfiapp_event_type != $requestedChange) {
                // change of event type: old qr needs to be deleted first
                Log::debug('Updating service #'.$service->id.', changed KonfiApp event type from '.$service->konfiapp_event_type.' to '.$requestedChange);
                Log::debug('Deleting old KonfiApp QR code '.$service->konfiapp_event_qr);
                $this->deleteQRCodeByCode($service->konfiapp_event_qr, $service->konfiapp_event_type);
                $code = $this->createQRCode($service);
                Log::debug('Got code '.$code);
                $service->update(
                    ['konfiapp_event_type' => $requestedChange, 'konfiapp_event_qr' => $code]
                );
                $service2 = Service::find($service->id);
                Log::debug('Updated service to code '.$service2->konfiapp_event_qr);
            }
        }
        return $service;
    }

    /**
     * Create a QR code for a service
     * @param Service $service
     * @return mixed
     * @throws Exception
     */
    public function createQRCode(Service $service)
    {
        $time = ($service->time == ':0') ? '' : ($service->time ?: '');
        $serviceTime = Carbon::createFromTimeString(trim($service->day->date->format('Y-m-d') . ' ' . $time));
        return ($this->requestData(
            'verwaltung/veranstaltungen/qr/add/',
            [
                'veranstaltungID' => $service->konfiapp_event_type,
                'dateStart' => $service->day->date->format('Y.m.d'),
                'dateEnd' => $service->day->date->format('Y.m.d'),
                'timeStart' => $serviceTime->format('H:i'),
                'timeEnd' => $serviceTime->clone()->addHour(3)->format('H:i'),
            ]
        ))->code;
    }

    /**
     * @param $code
     * @param $type
     * @throws Exception
     */
    public function deleteQRCodeByCode($code, $type)
    {
        $codes = $this->requestData(
            'verwaltung/veranstaltungen/qr/list/',
            [
                'veranstaltungID' => $type,
            ]
        )->detail;

        foreach ($codes as $qrcode) {
            if ($qrcode->code == $code) {
                $this->deleteQRCode($qrcode->id);
            }
        }
    }

    /**
     * Delete a qr associated with a service
     * @param string $id Code id
     * @throws Exception
     */
    public function deleteQRCode($id)
    {
        $this->requestData(
            'verwaltung/veranstaltungen/qr/delete/',
            [
                'id' => $id,
            ]
        );
    }

    /**
     * @param Service $service
     * @return Service
     * @throws Exception
     */
    public function handleServiceDelete(Service $service)
    {
        if ($service->konfiapp_event_qr != '') {
            $this->deleteQRCode($service->konfiapp_event_qr);
        }
        $service->update(['konfiapp_event_qr' => '']);
        return $service;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function listQRCodes()
    {
        return $this->requestData('verwaltung/veranstaltungen/qr/list/', ['veranstaltungID' => 682])->detail;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }


}
