<?php

namespace OAuth\Unit\UserData\Extractor;

use OAuth\UserData\Arguments\FieldsValues;
use OAuth\UserData\Arguments\LoadersMap;
use OAuth\UserData\Arguments\NormalizersMap;
use OAuth\UserData\Extractor\ExtractorInterface;
use OAuth\UserData\Extractor\LazyExtractor;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-08 at 10:56:49.
 */
class LazyExtractorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function testLazyLoadGet()
	{
		$constructorArgs = [
			FieldsValues::construct([
				ExtractorInterface::FIELD_UNIQUE_ID => 123,
				ExtractorInterface::FIELD_USERNAME
			]),
			NormalizersMap::construct([
				ExtractorInterface::FIELD_UNIQUE_ID => ExtractorInterface::FIELD_UNIQUE_ID,
			])->method(ExtractorInterface::FIELD_USERNAME, 'username'),
			LoadersMap::construct([
				'id' => [ExtractorInterface::FIELD_UNIQUE_ID],
				'profile' => [ExtractorInterface::FIELD_USERNAME]
			])
		];

		$profileData     = [
			'data' => [
				'nickname' => 'johnnydonny'
			]
		];

		$extractor = $this->getMock('\\OAuth\\UserData\\Extractor\\LazyExtractor', [
			'idLoader',
			'uniqueIdNormalizer',
			'profileLoader',
			'usernameNormalizer'
		], $constructorArgs);

		$extractor->expects($this->never())
			->method('idLoader');
		$extractor->expects($this->never())
			->method('uniqueIdNormalizer');
		$extractor->expects($this->once())
			->method('profileLoader')
			->with()
			->willReturn($profileData);
		$extractor->expects($this->once())
			->method('usernameNormalizer')
			->with($profileData)
			->willReturn($profileData[ 'data' ][ 'nickname' ]);

		/**
		 * @var \OAuth\UserData\Extractor\LazyExtractor $extractor
		 */
		$this->assertEquals(123, $extractor->getUniqueId()); // prefetched field, does not trigger loader and normalizer
		$this->assertEquals('johnnydonny', $extractor->getUsername()); // triggers the loader and the normalizer
		$this->assertEquals('johnnydonny', $extractor->getUsername()); // does not trigger them again
	}

	public function testUnsupportedField()
	{
		$extractor = new LazyExtractor();
		$this->assertNull($extractor->getUniqueId());
	}
}
