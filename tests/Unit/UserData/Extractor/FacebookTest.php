<?php

namespace OAuth\Unit\UserData\Extractor;

use OAuth\UserData\Extractor\Facebook;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-07 at 22:32:44.
 */
class FacebookTest extends \PHPUnit_Framework_TestCase
{
    const RESPONSE_PROFILE =
'{
  "id": "012345678",
  "name": "John Doe",
  "first_name": "John",
  "last_name": "Doe",
  "link": "https://www.facebook.com/johnnydonny",
  "birthday": "05/17/1987",
  "hometown": {
    "id": "111665038853258",
    "name": "Catania, Italy"
  },
  "location": {
    "id": "115353315143936",
    "name": "Rome, Italy"
  },
  "bio": "A life on the edge",
  "quotes": "fall seven times - stand up eight",
  "gender": "male",
  "email": "johndoe@hotmail.com",
  "website": "http://blog.foo.com Blog\nhttp://foo.com Portfolio",
  "timezone": 1,
  "locale": "it_IT",
  "verified": true,
  "updated_time": "2014-02-07T10:57:24+0000",
  "username": "johnnydonny"
}';

    const RESPONSE_IMAGE =
'{
  "data": {
    "url": "https://fbcdn-profile-a.akamaihd.net/something_n.jpg",
    "is_silhouette": false
  }
}';

    /**
     * @var Facebook
     */
    protected $extractor;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->extractor = new Facebook();
        $service = $this->getMockBuilder('\\OAuth\\OAuth2\\Service\\Facebook')
            ->disableOriginalConstructor()
            ->getMock();

        $service->expects($this->any())
            ->method('requestJSON')
            ->willReturnCallback(function ($arg) {
	            if ($arg == Facebook::REQUEST_PROFILE) {
		            return json_decode(FacebookTest::RESPONSE_PROFILE, TRUE);
	            } elseif ($arg == Facebook::REQUEST_IMAGE) {
		            return json_decode(FacebookTest::RESPONSE_IMAGE, TRUE);
	            }

	            return null;
            });

        /**
         * @var \OAuth\Common\Service\ServiceInterface $service
         */
        $this->extractor->setService($service);
    }

	public function testGetUniqueId()
	{
		$this->assertEquals('012345678', $this->extractor->getUniqueId());
	}

	public function testGetUsername()
	{
		$this->assertEquals('johnnydonny', $this->extractor->getUsername());
	}

	public function testGetFirstName()
	{
		$this->assertEquals('John', $this->extractor->getFirstName());
	}

	public function testGetLastName()
	{
		$this->assertEquals('Doe', $this->extractor->getLastName());
	}

	public function testGetFullName()
	{
		$this->assertEquals('John Doe', $this->extractor->getFullName());
	}

	public function testGetEmail()
	{
		$this->assertEquals('johndoe@hotmail.com', $this->extractor->getEmail());
	}

	public function testGetDescription()
	{
		$this->assertEquals('A life on the edge', $this->extractor->getDescription());
	}

	public function testGetProfileUrl()
	{
		$this->assertEquals('https://www.facebook.com/johnnydonny', $this->extractor->getProfileUrl());
	}

	public function testGetLocation()
	{
		$this->assertEquals('Rome, Italy', $this->extractor->getLocation());
	}

	public function testGetWebsites()
	{
		$expected = [
			'http://blog.foo.com',
			'http://foo.com'
		];
		$this->assertEquals($expected, $this->extractor->getWebsites());
	}

	public function testGetImageUrl()
	{
		$this->assertEquals('https://fbcdn-profile-a.akamaihd.net/something_n.jpg', $this->extractor->getImageUrl());
	}

	public function testIsEmailVerified()
	{
		$this->assertTrue($this->extractor->isEmailVerified());
	}

	public function testGetExtra()
	{
		$extras = $this->extractor->getExtras();
		$this->assertArrayHasKey('birthday', $extras);
		$this->assertArrayHasKey('hometown', $extras);
		$this->assertArrayHasKey('quotes', $extras);

		$this->assertArrayNotHasKey('id', $extras);
		$this->assertArrayNotHasKey('name', $extras);
		$this->assertArrayNotHasKey('first_name', $extras);
	}
}
