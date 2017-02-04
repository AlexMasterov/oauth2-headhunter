# HeadHunter.ru Provider for OAuth 2.0 Client

[![Latest Stable Version](https://poser.pugx.org/alexmasterov/oauth2-headhunter/v/stable)](https://packagist.org/packages/alexmasterov/oauth2-headhunter)
[![License](https://img.shields.io/packagist/l/alexmasterov/oauth2-headhunter.svg)](https://github.com/AlexMasterov/oauth2-headhunter/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/AlexMasterov/oauth2-headhunter.svg)](https://travis-ci.org/AlexMasterov/oauth2-headhunter)
[![Code Coverage](https://scrutinizer-ci.com/g/AlexMasterov/oauth2-headhunter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AlexMasterov/oauth2-headhunter/?branch=master)
[![Code Quality](https://scrutinizer-ci.com/g/AlexMasterov/oauth2-headhunter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AlexMasterov/oauth2-headhunter/?branch=master)

This package provides [HeadHunter.ru](https://hh.ru) OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
composer require alexmasterov/oauth2-headhunter
```

## Usage

### Configuration
```php
$provider = new AlexMasterov\OAuth2\Client\Provider\HeadHunter([
    'clientId'     => '{client_id}',
    'clientSecret' => '{client_secret}',
    'redirectUri'  => '{redirect_uri}',
    'state'        => '{state}',
]);
```

### Authorization
```php
if (!empty($_GET['error'])) {
    // Got an error, probably user denied access
    exit('Got error: ' . $_GET['error']);
}

if (empty($_GET['code'])) {
    // If we don't have an authorization code then get one
    $provider->authorize();
}

// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

// Optional: Now you have a token you can look up a users profile data
try {
    // We got an access token, let's now get the owner details
    $ownerDetails = $provider->getResourceOwner($token);

    // Use these details to create a new profile
    printf('Hello, %s!', $ownerDetails->getFirstName());
} catch (\Exception $e) {
    // Failed to get user details
    exit('Something went wrong: ' . $e->getMessage());
}

// Use this to interact with an API on the users behalf
echo $token->accessToken;
```
