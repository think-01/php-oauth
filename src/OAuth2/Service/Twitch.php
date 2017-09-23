<?php

namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

class Twitch extends AbstractService
{
    /**
     * Scopes defined at:
     * https://github.com/justintv/Twitch-API/blob/master/authentication.md#scopes
     */

    /**
     * Read access to non-public user information, such as email address.
     */
    const SCOPE_USER_READ = 'user_read';
    const SCOPE_USER_EMAIL = 'user:read:email';
    const SCOPE_USER_EDIT = 'user:edit';

    /**
     * Ability to ignore or unignore on behalf of a user.
     */
    const SCOPE_BLOCKS_EDIT = 'user_blocks_edit';

    /**
     * Read access to a user's list of ignored users.
     */
    const SCOPE_BLOCKS_READ = 'user_blocks_read';

    /**
     * Access to manage a user's followed channels.
     */
    const SCOPE_CHANNEL_READ = 'channel_read';

    /**
     * Write access to channel metadata (game, status, etc).
     */
    const SCOPE_CHANNEL_EDITOR = 'channel_editor';

    /**
     * Access to trigger commercials on channel.
     */
    const SCOPE_CHANNEL_COMMERCIAL = 'channel_commercial';

    /**
     * Ability to reset a channel's stream key.
     */
    const SCOPE_CHANNEL_STREAM = 'channnel_stream';

    /**
     * Read access to all subscribers to your channel.
     */
    const SCOPE_CHANNEL_SUBSCRIPTIONS = 'channel_subscriptions';

    /**
     * Read access to subscriptions of a user.
     */
    const SCOPE_USER_SUBSCRIPTIONS = 'user_subscriptions';

    /**
     * Read access to check if a user is subscribed to your channel.
     */
    const SCOPE_CHANNEL_CHECK_SUBSCRIPTION = 'channel_check_subscription';

    /**
     * Ability to log into chat and send messages.
     */
    const SCOPE_CHAT_LOGIN = 'chat_login';

    /**
     * Ability to read the channel feed interactions of a user.
     */
    const SCOPE_CHANNEL_FEED_READ = 'channel_feed_read';

    /**
     * Add new posts or delete existing ones to the channel feed of a user.
     * Alows write acces to reactions in the name of the user.
     */
    const SCOPE_CHANNEL_FEED_WRITE = 'channel_feed_write';

    protected $baseApiUri = 'https://api.twitch.tv/kraken/';
    protected $authorizationEndpoint = 'https://api.twitch.tv/kraken/oauth2/authorize';
    protected $accessTokenEndpoint = 'https://api.twitch.tv/kraken/oauth2/token';

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);

        if (isset($data[ 'expires_in' ]))
        {
            $token->setLifeTime($data[ 'expires_in' ]);
        }

        if (isset($data[ 'refresh_token' ]))
        {
            $token->setRefreshToken($data[ 'refresh_token' ]);
            unset($data[ 'refresh_token' ]);
        }

        unset($data['access_token']);
        unset($data[ 'expires_in' ]);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraApiHeaders()
    {
        return array(
            'Accept' => 'application/vnd.twitchtv.'.$this->apiVersion.'+json',
            'Client-ID' => $this->credentials->getConsumerId()
        );
    }

    public function getAuthorizationUri(array $additionalParameters = [])
    {
        $parameters = array_merge(
            array(
                'client_id'     => $this->credentials->getConsumerId(),
                'redirect_uri'  => $this->credentials->getCallbackUrl(),
                'response_type' => 'code',
            ),
            $additionalParameters
        );

        $parameters[ 'scope' ] = implode(' ', $this->scopes);

        if ($this->needsStateParameterInAuthUrl())
        {
            if (!isset($parameters[ 'state' ]))
            {
                $parameters[ 'state' ] = $this->generateAuthorizationState();
            }
            $this->storeAuthorizationState($parameters[ 'state' ]);
        }

        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        $url->getQuery()->modify($parameters);

        return $url;
    }
}