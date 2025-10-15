<?php

namespace Esposimo\Azure\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ManageIdentity
{
    /**
     * The Azure Instance Metadata Service endpoint for managed identities
     */
    private const MANAGED_IDENTITY_ENDPOINT = 'http://169.254.169.254/metadata/identity/oauth2/token';

    /**
     * The API version for the IMDS endpoint
     */
    private const API_VERSION = '2018-02-01';

    /**
     * The HTTP client used to make the token request
     * @var Client
     */
    private Client $httpClient;

    /**
     * The resource scope for the token request
     * @var string|null
     */
    private ?string $resource = null;

    /**
     * Constructor
     * @param Client|null $httpClient
     */
    public function __construct(?Client $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new Client();
    }

    /**
     * Sets the resource scope for the token request
     *
     * @param string $resource
     * @return $this
     */
    public function setResource(string $resource): self
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Retrieves the managed identity token from Azure IMDS
     *
     * @return string
     * @throws GuzzleException
     * @throws \RuntimeException
     */
    public function getToken(): string
    {
        if (!$this->resource) {
            throw new \RuntimeException('Resource scope must be set before requesting token');
        }

        $response = $this->httpClient->get(self::MANAGED_IDENTITY_ENDPOINT, [
            'headers' => [
                'Metadata' => 'true'
            ],
            'query' => [
                'api-version' => self::API_VERSION,
                'resource' => $this->resource
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if (!isset($result['access_token'])) {
            throw new \RuntimeException('Invalid response from IMDS endpoint');
        }

        return $result['access_token'];
    }
}