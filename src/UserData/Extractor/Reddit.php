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
class Reddit extends LazyExtractor {

    /**
     * Request contants
     */
    const REQUEST_PROFILE = 'https://oauth.reddit.com/api/v1/me.json';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            FieldsValues::construct([
                self::FIELD_UNIQUE_ID,
                self::FIELD_USERNAME,
                self::FIELD_PROFILE_URL,
                self::FIELD_IMAGE_URL,
            ]),
            self::getDefaultNormalizersMap()
                ->paths([
                    self::FIELD_UNIQUE_ID   => 'id',
                    self::FIELD_USERNAME    => 'name'
                ])
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }

    protected function profileUrlNormalizer($data)
    {
        return isset($data['name']) ? 'https://www.reddit.com/user/'.$data['name'] : null;
    }

    protected function imageUrlNormalizer($data)
    {
        return isset($data[ 'pref_show_snoovatar' ]) && $data[ 'pref_show_snoovatar' ] ? 'https://www.reddit.com/user/'.$data['name'].'/snoo' : NULL;
    }

    protected function websitesNormalizer($data)
    {
        return [];
    }
}