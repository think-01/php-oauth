<?php

/*
 * This file is part of the php-oauth package <https://github.com/logical-and/php-oauth>.
 *
 * (c) Oryzone, developed by Luciano Mammino <lmammino@oryzone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Arguments\FieldsValues;
use OAuth\UserData\Utils\ArrayUtils;
use OAuth\UserData\Utils\StringUtils;

/**
 * Class Facebook
 *
 * @package OAuth\UserData\Extractor
 */
class Twitch extends LazyExtractor {

    /**
     * Request contants
     */
    const REQUEST_PROFILE = 'https://api.twitch.tv/kraken/user';
    //const REQUEST_IMAGE   = '/me/picture?type=large&redirect=false';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            FieldsValues::construct([
                self::FIELD_UNIQUE_ID,
                self::FIELD_USERNAME,
                self::FIELD_FIRST_NAME,
                self::FIELD_LAST_NAME,
                self::FIELD_EMAIL,
                self::FIELD_PROFILE_URL,
                self::FIELD_IMAGE_URL,
            ]),
            self::getDefaultNormalizersMap()
                ->paths([
                    self::FIELD_UNIQUE_ID   => '_id',
                    self::FIELD_USERNAME    => 'display_name',
                    self::FIELD_FULL_NAME   => 'thgink01',
                    self::FIELD_EMAIL       => 'email',
                    self::FIELD_IMAGE_URL => 'logo',
                    self::FIELD_PROFILE_URL => '_links.self',
                ])
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }

    protected function firstNameNormalizer()
    {
        $fullName = $this->getField(self::FIELD_FULL_NAME);
        if ($fullName)
        {
            $names = explode(' ', $fullName);

            return $names[ 0 ];
        }

        return NULL;
    }

    protected function lastNameNormalizer()
    {
        $fullName = $this->getField(self::FIELD_FULL_NAME);
        if ($fullName)
        {
            $names = explode(' ', $fullName);

            return $names[ sizeof($names) - 1 ];
        }

        return NULL;
    }

    protected function profileUrlNormalizer($data)
    {
        return isset($data[ '_links' ]) ? $data[ '_links' ][ 'self' ] : NULL;
    }

    protected function websitesNormalizer($data)
    {
        return [];
    }
}