<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Token\StdOAuth2Token;

/**
 * Amazon service.
 *
 * @author Flávio Heleno <flaviohbatista@gmail.com>
 * @link https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf
 */
class Amazon extends AbstractService {

	/**
	 * Defined scopes
	 *
	 * @link https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf
	 */
	const SCOPE_PROFILE     = 'profile';
	const SCOPE_POSTAL_CODE = 'postal_code';

	protected $baseApiUri = 'https://api.amazon.com/';
	protected $authorizationEndpoint = 'https://www.amazon.com/ap/oa';
	protected $accessTokenEndpoint = 'https://www.amazon.com/ap/oatoken';
	protected $authorizationMethod = self::AUTHORIZATION_METHOD_HEADER_BEARER;

	/**
	 * {@inheritdoc}
	 */
	protected function parseAccessTokenResponse($responseBody)
	{
		$data = json_decode($responseBody, TRUE);

		if (NULL === $data || !is_array($data))
		{
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif (isset($data[ 'error_description' ]))
		{
			throw new TokenResponseException('Error in retrieving token: "' . $data[ 'error_description' ] . '"');
		}
		elseif (isset($data[ 'error' ]))
		{
			throw new TokenResponseException('Error in retrieving token: "' . $data[ 'error' ] . '"');
		}

		$token = new StdOAuth2Token();
		$token->setAccessToken($data[ 'access_token' ]);
		$token->setLifeTime($data[ 'expires_in' ]);

		if (isset($data[ 'refresh_token' ]))
		{
			$token->setRefreshToken($data[ 'refresh_token' ]);
			unset($data[ 'refresh_token' ]);
		}

		unset($data[ 'access_token' ]);
		unset($data[ 'expires_in' ]);

		$token->setExtraParams($data);

		return $token;
	}
}
