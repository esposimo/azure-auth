# Azure PHP Auth Client
A lightweight PHP library for managing authentication to Microsoft Azure APIs via Microsoft Entra ID (formerly Azure AD).
It currently supports authentication based on a Service Principal using Client ID and Client Secret.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/esposimo/assertion.svg?style=flat-square)](https://packagist.org/packages/esposimo/azure-auth)
[![Total Downloads](https://img.shields.io/packagist/dt/esposimo/assertion.svg?style=flat-square)](https://packagist.org/packages/esposimo/azure-auth)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Installation
Use Composer to add the library to your project:

```bash
composer require esposimo/azure-auth
```

## Configuration and Authentication (Client ID / Secret)

This method is ideal for local development environments, CI/CD pipelines, or applications running outside of Azure.

1. **Prerequisites**
   
To use this method, you must register your application in Microsoft Entra ID and obtain the following values
- `TENANT_ID`: The unique ID (GUID) of your Microsoft Entra tenant
- `CLIENT_ID`: The Application (Client) ID for your registered app
- `CLIENT_SECRET`: The client secret generated for the application.

2. **Usage Example**
   
Instantiate the client and call the `getToken()` method


## Usage
Example with Azure Data Explorer URL

```php
<?php
use Esposimo\AzureAuth\OAuth;

// Assume these values are loaded securely 
// (e.g., from environment variables or a .env file)
$tenantId     = getenv('AZURE_TENANT_ID');
$clientId     = getenv('AZURE_CLIENT_ID');
$clientSecret = getenv('AZURE_CLIENT_SECRET');

// URI of the Azure resource to access (e.g., Key Vault)
$resourceUri = 'https://vault.azure.net'; 

try {
    $authClient = new OAuth($tenantId, $clientId, $clientSecret, $resourceUri);
    
    // Get the token
    $tokenString = $authClient->getToken();

    // Use the token for your REST API call
    // Example: Include in the HTTP header for the Key Vault API call:
    // 'Authorization: Bearer ' . $tokenString;

} catch (\Exception $e) {
    echo "Authentication Error: " . $e->getMessage();
}
```

## Future Roadmap: Managed Identities (MI) Support
I plan to expand this library to support Azure's secret-less authentication method: Managed Identities (MI).

**Planned Functionality**
In the future, the library will support token retrieval from Azure environments (VMs, App Service, Container Apps, Azure Functions) using Managed Identities 
