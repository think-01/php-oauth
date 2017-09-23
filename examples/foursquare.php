<?php

/**
 * Example of retrieving an authentication token of the Foursquare service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Foursquare;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['foursquare']['key'],
    $servicesCredentials['foursquare']['secret'],
    $currentUri
);

// Instantiate the Foursquare service using the credentials, http client and storage mechanism for the token
/** @var $foursquareService Foursquare */
$foursquareService = $serviceFactory->createService('foursquare', $credentials, $storage);

if ($foursquareService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $foursquareService->retrieveAccessTokenByGlobReqArgs()->requestJSON('users/self');

	// Show some of the resultant data
	echo 'Your unique foursquare user id is: ' . $result['response']['user']['id'] . ' and your name is ' . $result['response']['user']['firstName'] . $result['response']['user']['lastName'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$foursquareService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Foursquare!</a>";
}