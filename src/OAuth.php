<?php

namespace Esposimo\Azure\Auth;

use \GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

class OAuth
{

    /**
     * Contiene la classe http che gestisce le chiamate
     * @var HttpClient
     */
    protected HttpClient $httpClient;


    /**
     * Contiene il client id
     * @var string
     */
    protected string $client_id;

    /**
     * Contiene il client secret
     * @var string
     */
    protected string $client_secret;

    /**
     * Contiene il tenant usato per costruire la chiamata all'API di login
     * @var string
     */
    protected string $tenant;

    /**
     * Contiene la risorsa o le risorse da chiedere
     * @var string|array
     */
    protected string|array $resources;

    /**
     * Contiene la data di scadenza
     * @var \DateTime|null
     */
    protected ?\DateTime $expires = null;

    /**
     * Contiene il token da usare per le chiamate API
     * @var string
     */
    protected string $token;


    /**
     * @throws GuzzleException
     */
    public function __construct(
        string $client_id,
        string $client_secret,
        string $tenant,
        string|array $resources)
    {
        $this->client_id        = $client_id;
        $this->client_secret    = $client_secret;
        $this->tenant           = $tenant;
        $this->resources        = $resources;
        $this->createResource();
    }

    /**
     * Effettuo una nuova richiesta di token
     * @return void
     * @throws GuzzleException
     */
    private function createResource(): void
    {
        $this->httpClient = new HttpClient(
            [
                'base_uri' => 'https://login.microsoftonline.com'
            ]);
        $formParams = [
            "grant_type" => "client_credentials",
            "resource" => $this->resources,
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret
        ];
        try {
        $response = $this->httpClient->post(sprintf('%s/oauth2/token', $this->tenant),
            ['form_params' => $formParams]);
        $body = json_decode($response->getBody());
        $expire = new \DateTime();
        $expire->add(new \DateInterval('PT' . $body->expires_in . 'S'));
        $this->token = $body->access_token;
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Restituisce il token. Se è scaduto o non è stato mai richiesto (non esiste la data di scadenza) lo richiedo
     * @return string
     * @throws GuzzleException
     */
    public function getToken(): string
    {
        if (is_null($this->expires) || $this->expires < new \DateTime())
        {
            $this->createResource();
        }
        return $this->token;
    }

}