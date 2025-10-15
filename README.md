# Azure PHP Auth Client

A lightweight PHP library for managing authentication to Microsoft Azure APIs via Microsoft Entra ID (formerly Azure
AD). This library provides a simple and efficient way to handle OAuth 2.0 authentication flows when interacting with
Azure services. Designed with simplicity in mind, it abstracts away the complexity of token management and
authentication handshakes, allowing developers to focus on their core application logic rather than authentication
implementation details.
It currently supports authentication based on a Service Principal using Client ID and Client Secret and Managed Identities.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/esposimo/assertion.svg?style=flat-square)](https://packagist.org/packages/esposimo/azure-auth)
[![Total Downloads](https://img.shields.io/packagist/dt/esposimo/assertion.svg?style=flat-square)](https://packagist.org/packages/esposimo/azure-auth)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Installation
Use Composer to add the library to your project:

```bash
composer require esposimo/azure-auth
```

## Configuration and Authentication (Managed Identities)

When your application is running in an Azure environment with Managed Identity enabled (e.g., Azure VMs, App Services,
or Azure Functions), you can use the simplified authentication flow:

1. **Prerequisites**
   
To use this method, you must register your application in Microsoft Entra ID and grant it access to the Azure
resources you want to access.

2. **Usage Example**
   
```php
<?php
use \Esposimo\Azure\Auth\AzureAuthenticationProvider;

$azureTokenProvider = new AzureAuthenticationProvider(AzureAuthenticationProvider::MANAGED_IDENTITY);
$azureTokenProvider->setResourceUri('https://vault.azure.net'); // URI of the Azure resource to access (e.g., Key Vault)
$tokenString = $azureTokenProvider->getAccessToken();

// Use the token for your REST API call
// Example: Include in the HTTP header for the Key Vault API call:
// 'Authorization: Bearer ' . $tokenString;
```

## Configuration and Authentication (Client ID / Secret)

This method is ideal for local development environments, CI/CD pipelines, or applications running outside of Azure.

1. **Prerequisites**
   
To use this method, you must register your application in Microsoft Entra ID and obtain the following values
- `TENANT_ID`: The unique ID (GUID) of your Microsoft Entra tenant
- `CLIENT_ID`: The Application (Client) ID for your registered app
- `CLIENT_SECRET`: The client secret generated for the application.
- `SCOPE`: The scope of the resource you want to access. For example, `https://vault.azure.net/.default`

2. **Usage Example**

```php
<?php
use \Esposimo\Azure\Auth\AzureAuthenticationProvider;

$tenant_id = '<your-tenant-id>';
$client_id = '<your-client-id>';
$client_secret = '<your-client-secret>';
$scope = '<your-scope>';

$azureTokenProvider = new AzureAuthenticationProvider(
    AzureAuthenticationProvider::SERVICE_PRINCIPAL, 
    $client_id, 
    $client_secret, 
    $tenant, 
    $scope
);
$tokenString = $azureTokenProvider->getAccessToken();

// Use the token for your REST API call
// Example: Include in the HTTP header for the Key Vault API call:
// 'Authorization: Bearer ' . $tokenString;
```

## Usage
Example with Azure Data Explorer URL and Service Principal method

```php
<?php
use \Esposimo\Azure\Auth\AzureAuthenticationProvider;

// Assume these values are loaded securely 
// (e.g., from environment variables or a .env file)
$tenantId     = getenv('AZURE_TENANT_ID');
$clientId     = getenv('AZURE_CLIENT_ID');
$clientSecret = getenv('AZURE_CLIENT_SECRET');

// URI of the Azure resource to access (e.g., Key Vault)
$resourceUri = 'https://vault.azure.net'; 

try {
    $azureTokenProvider = new AzureAuthenticationProvider(
        AzureAuthenticationProvider::SERVICE_PRINCIPAL, 
        $client_id, 
        $client_secret, 
        $tenant, 
        $scope
    );
    $tokenString = $azureTokenProvider->getAccessToken();

    // Use the token for your REST API call
    // Example: Include in the HTTP header for the Key Vault API call:
    // 'Authorization: Bearer ' . $tokenString;

} catch (\Exception $e) {
    echo "Authentication Error: " . $e->getMessage();
}
```

