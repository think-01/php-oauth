<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Twitter;

class TwitterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.twitter.com/oauth/request_token',
            (string) $service->getRequestTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertTrue(
            in_array(
                strtolower((string) $service->getAuthorizationEndpoint()),
                array(\OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHENTICATE, \OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE)
            )
        );

        $service->setAuthorizationEndpoint( \OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE );

        $this->assertTrue(
            in_array(
                strtolower((string) $service->getAuthorizationEndpoint()),
                array(\OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHENTICATE, \OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE)
            )
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::setAuthorizationEndpoint
     */
    public function testSetAuthorizationEndpoint()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Exception\\Exception');

        $service->setAuthorizationEndpoint('foo');
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.twitter.com/oauth/access_token',
            (string) $service->getAccessTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var Twitter|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\Twitter', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue(null));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        /** @var Twitter|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\Twitter', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('notanarray'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        /** @var Twitter|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\Twitter', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('foo=bar'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
        /** @var Twitter|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock('\\OAuth\\OAuth1\\Service\\Twitter', ['httpRequest'], [
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        ]);

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue(
            'oauth_callback_confirmed=false'
        ));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
        /** @var Twitter|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock('\\OAuth\\OAuth1\\Service\\Twitter', ['httpRequest'], [
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        ]);

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue(
            'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
        ));

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var Twitter|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock('\\OAuth\\OAuth1\\Service\\Twitter', ['httpRequest'], [
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $storage,
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        ]);

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=bar'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var Twitter|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock('\\OAuth\\OAuth1\\Service\\Twitter', ['httpRequest'], [
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $storage,
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        ]);

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue(
            'oauth_token=foo&oauth_token_secret=bar'
        ));

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }
}
