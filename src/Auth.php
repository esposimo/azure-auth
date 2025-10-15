<?php

namespace Esposimo\Azure\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

class Auth
{

    /**
     * Represents the service principal type.
     */
    const SERVICE_PRINCIPAL = 'service_principal';

    /**
     * Represents the managed identity type.
     */
    const MANAGED_IDENTITY = 'managed_identity';

    /**
     * Represents the client identifier
     * @var string|null
     */
    private ?string $clientId = null;

    /**
     * Represents the client secret
     * @var string|null
     */
    private ?string $clientSecret = null;

    /**
     * Represents the tenant ID
     * @var string|null
     */
    private ?string $tenantId = null;

    /**
     * Represents the scope variable
     * @var string|null
     */
    private ?string $scope = null;

    /**
     * Represents the type of authentication.
     * @var string
     */
    private string $authType;

    /**
     * Represents the HTTP client instance used for making HTTP requests.
     * @var Client
     */
    private Client $httpClient;

    /**
     * Initializes the class instance with the provided configuration parameters.
     *
     * @param string $authType The authentication type.
     * @param string|null $clientId The client ID. Ignored if $authType is 'managed_identity'.
     * @param string|null $clientSecret The client secret. Ignored if $authType is 'managed_identity'
     * @param string|null $tenantId The tenant ID. Ignored if $authType is 'managed_identity'
     * @param string|null $scope The authentication scope. Ignored if $authType is 'managed_identity'
     *
     * @return void
     */
    public function __construct(
        string  $authType,
        ?string $clientId = null,
        ?string $clientSecret = null,
        ?string $tenantId = null,
        ?string $scope = null
    )
    {
        $this->authType = $authType;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tenantId = $tenantId;
        $this->scope = $scope;
        $this->httpClient = new Client();

        $this->validateConfig();
    }

    /**
     * Validates the configuration required for the service principal authentication.
     *
     * @return void
     * @throws InvalidArgumentException If required parameters for service principal authentication are missing.
     */
    private function validateConfig(): void
    {
        if ($this->authType === 'service_principal' &&
            (!$this->clientId || !$this->clientSecret || !$this->tenantId || !$this->scope)) {
            throw new InvalidArgumentException('Service Principal authentication requires clientId, clientSecret, tenantId and scope');
        }
    }

    /**
     * Retrieves the access token based on the authentication type.
     *
     * @return string The access token.
     * @throws InvalidArgumentException|\Exception If an invalid authentication type is provided or auth configuration are invalid.
     */
    public function getAccessToken(): string
    {
        return match ($this->authType) {
            self::SERVICE_PRINCIPAL => $this->getServicePrincipalToken(),
            self::MANAGED_IDENTITY => $this->getManagedIdentityToken(),
            default => throw new InvalidArgumentException('Invalid authentication type')
        };
    }

    /**
     * Retrieves an authentication token for the service principal.
     *
     * @return string The authentication token obtained from the OAuth service.
     * @throws GuzzleException
     */
    private function getServicePrincipalToken(): string
    {
        $oAuth = new OAuth($this->clientId, $this->clientSecret, $this->tenantId, $this->scope);
        return $oAuth->getToken();
    }

    /**
     * Retrieves a managed identity access token from the Azure Instance Metadata Service.
     *
     * @return string The access token retrieved for the managed identity.
     * @throws GuzzleException
     */
    private function getManagedIdentityToken(): string
    {
        $manageIdentity = new ManageIdentity();
        $manageIdentity->setResource($this->scope);
        return $manageIdentity->getToken();
    }


}