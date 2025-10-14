# Introduction
Very simple class for authenticatin with OAuth 2.0 / Entità Servizio

## Usage
Example with Azure Data Explorer URL

```php
<?php

$client_id      = "<client-id>";
$client_secret  = "<client_secret>";
$tenant         = "<tenant_id>";

$oAuth = new \Esposimo\AzureAuth\OAuth(
  $client_id,
  $client_secret,
  $tenant, 'https://<resource>.westeurope.kusto.windows.net/');

echo $oAuth->getToken(); // return token for API Requests
