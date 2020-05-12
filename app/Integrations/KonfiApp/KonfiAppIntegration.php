<?php


namespace App\Integrations\KonfiApp;


use App\City;
use App\Integrations\AbstractIntegration;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class KonfiAppIntegration extends AbstractIntegration
{

    protected const API_URL = 'https://api.konfiapp.de/v2/';

    protected $apiKey = '';

    /** @var Client */
    protected $client;

    public function __construct($apiKey)
    {
        $this->setApiKey($apiKey);
        $this->setClient(new Client(['base_uri' => self::API_URL]));
    }

    public function listEventTypes() {
        return collect($this->requestData('GET', 'verwaltung/veranstaltungen')->veranstaltungen);
    }

    protected function requestData($requestType, $path, $arguments = []) {
        $response = $this->request($requestType, $path, $arguments);
        if ($response->getStatusCode() != 200) throw new Exception ('Could not retrieve event types from KonfiApp.');
        return json_decode((string)$response->getBody())->data;
    }

    protected function request($requestType, $path, $arguments = []): ResponseInterface {
        return $this->client->request($requestType, $path, [
            'query' => [
                'apikey' => $this->apiKey,
            ],
            'form_params' => $arguments,
        ]);
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
