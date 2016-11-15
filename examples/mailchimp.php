<?php

/**
 * Example of retrieving an authentication token of the Mailchimp service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Hannes Van De Vreken <vandevreken.hannes@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Mailchimp;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

$_SERVER['SERVER_PORT'] = 80;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['mailchimp']['key'],
    $servicesCredentials['mailchimp']['secret'],
    $currentUri
);

// Instantiate the Mailchimp service using the credentials, http client and storage mechanism for the token
/** @var $mailchimpService Mailchimp */
$mailchimpService = $serviceFactory->createService('mailchimp', $credentials, $storage, array());

if ($mailchimpService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $mailchimpService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/users/profile.json');

	// Show some of the resultant data
	header('Content-Type: application/json');
	echo $result; exit;

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$mailchimpService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Mailchimp!</a>";
}