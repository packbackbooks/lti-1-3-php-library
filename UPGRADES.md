## 5.x to 6.0

### Dropped support for PHP 7 and PHP-JWT 5

This library now requires PHP 8 and firebase/php-jwt 6.

### Removed `ImsStorage` classes

Everything in the `Packback\Lti1p3\ImsStorage` namespace has been removed, specifically the `Packback\Lti1p3\ImsStorage\ImsCache` and `Packback\Lti1p3\ImsStorage\ImsCookie`. If you were using these classes, you will need to implement your own custom storage services. See the [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#sample-data-store-implementations) for an example.

### Changed how the OIDC Login URL is retrieved, deprecated the `Redirect` object

When redirecting to the OIDC Login URL, the `Packback\Lti1p3\LtiOidcLogin::getOidcLoginUrl()` method should be used to retrieve the URL. Your application should use this to build the redirect response in whatever way is appropriate for your framework. This replaces, `Packback\Lti1p3\LtiOidcLogin::doOidcLoginRedirect()` which returned a `Redirect` object. See: https://github.com/packbackbooks/lti-1-3-php-library/pull/116

```php
// instead of doing this:
$redirect = $oidLogin->doOidcLoginRedirect($launchUrl, $request);
return redirect($redirect->getRedirectUrl());

// you should do this:
return redirect($oidLogin->getRedirectUrl($launchUrl, $request));
```

### Removed deprecated methods

The following methods have been removed:

* `Packback\Lti1p3\JwksEndpoint::outputJwks()` - use `getPublicJwks()` to build your own output
* `Packback\Lti1p3\LtiDeepLink::outputResponseForm()` - use `getResponseJwt()` to build your own output
* `Packback\Lti1p3\LtiDeepLinkResource::getTarget()` - consider using `getIframe()` or `getWindow()` instead
* `Packback\Lti1p3\LtiDeepLinkResource::setTarget()` - consider using `setIframe()` or `setWindow()` instead
* `Packback\Lti1p3\Redirect::doHybridRedirect()`
* `Packback\Lti1p3\Redirect::getRedirectUrl()`

## 5.5 to 5.6

No breaking changes were introduced. However, going forward when processing a `LtiMessageLaunch`, it is recommended to do `$message->initialize($request);` instead of the previous `$message->validate($request);` to support potential migrations.

## 4.0 to 5.0

### Changes to `ICache` methods

Version 5.0 introduced changes to the `Packback\Lti1p3\Interfaces\ICache` interface.

* The method `checkNonce()` was renamed to `checkNonceIsValid()`.
* A second required argument (`$state`) was added to the `cacheNonce()` and `checkNonceIsValid()` methods.
* Stricter typing was introduced for several methods: `getLaunchData`, `cacheLaunchData`, `cacheNonce`, `cacheAccessToken`, `getAccessToken`, and `clearAccessToken`.

Stricter typing was added to methods on several interfaces.

* On `ICache` the following methods are now more strictly typed: `getLaunchData`, `cacheLaunchData`, `cacheNonce`, `cacheAccessToken`, `getAccessToken`, and `clearAccessToken`.
* On `ICookie` the following methods are now more strictly typed: `getCookie`, and `setCookie`.

### Stricter typing on `ICache` and `ICookie` methods

Arguments and return types are now strictly typed for methods on the cache and cookie interfaces. Custom implementations of these objects should be updated to adhere to the new typing requirements. View the interface definitions for [`Packback\Lti1p3\Interfaces\ICache`](https://github.com/packbackbooks/lti-1-3-php-library/blob/master/src/Interfaces/ICache.php) and [`Packback\Lti1p3\Interfaces\ICookie`](https://github.com/packbackbooks/lti-1-3-php-library/blob/master/src/Interfaces/ICookie.php) to see specific typing requirements.

## 3.0 to 4.0

### New methods implemented on the `ILtiServiceConnector`

Version 4.0 introduced changes to the `Packback\Lti1p3\Interfaces\ILtiServiceConnector` interface, adding the following methods:

* `setDebuggingMode()`
* `makeRequest()`
* `getRequestBody()`

## 2.0 to 3.0

### New method implemented on the `ICache`

Version 3.0 introduced changes to the `Packback\Lti1p3\Interfaces\ICache` interface, adding one method: `clearAccessToken()`. This method must be implemented to any custom implementations of the interface. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#cache) contains an example.

### Using `GuzzleHttp\Client` instead of curl

The `Packback\Lti1p3\LtiServiceConnector` now uses Guzzle instead of curl to make requests. This puts control of this client and its configuration in the hands of the developer. The section below contains information on implementing this change.

### Changes to the `LtiServiceConnector` and LTI services

The implementation of the `Packback\Lti1p3\LtiServiceConnector` changed to act as a general API Client for the various LTI service (Assignment Grades, Names Roles Provisioning, etc.) Specifically, the constructor for the following classes now accept different arguments:

* `LtiAssignmentGradesService`
* `LtiCourseGroupsService`
* `LtiNamesRolesProvisioningService`
* `LtiServiceConnector`

The `LtiServiceConnector` now only accepts an `ICache` and `GuzzleHttp\Client`, and does not need an `ILtiRegistration`. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#installation) contains an example of how to implement the service connector and configure the client.

The other LTI services now accept an `ILtiServiceConnector`, `ILtiRegistration`, and `$serviceData` (the registration was added as a new argument since it is no longer required for the `LtiServiceConnector`).

## 1.0 to 2.0

### Renamed Interfaces

A standard naming convention was implemented for interfaces: `Packback\Lti1p3\Interfaces\IObject`. Any implementations of these interfaces should be renamed:

* `Cache` to `ICache`
* `Cookie` to `ICookie`
* `Database` to `IDatabase`
* `LtiRegistrationInterface` to `ILtiRegistration`
* `LtiServiceConnectorInterface` to `ILtiServiceConnector`
* `MessageValidator` to `IMessageValidator`

### New methods implemented on the `ICache`

Version 2.0 introduced changes to the `Packback\Lti1p3\Interfaces\ICache` interface, adding two new methods: `cacheAccessToken()` and `getAccessToken()`. These methods must be implemented to any custom implementations of the interface. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#cache) contains an example.
