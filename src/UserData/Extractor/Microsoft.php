<?php
/**
 * Created by IntelliJ IDEA.
 * User: slawek@t01.pl
 * Date: 2016-07-20
 * Time: 09:27
 */
namespace OAuth\UserData\Extractor;

use OAuth\UserData\Arguments\FieldsValues;

/**
 * Class Google
 *
 * @package OAuth\UserData\Extractor
 */
class Microsoft extends LazyExtractor {

    const REQUEST_PROFILE = 'https://apis.live.net/v5.0/me';

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
                self::FIELD_EMAILS,
                self::FIELD_EXTRA,
            ]),
            self::getDefaultNormalizersMap()
                ->add([
                    self::FIELD_UNIQUE_ID      => 'id',
                    self::FIELD_USERNAME       => 'name',
                    self::FIELD_FIRST_NAME     => 'first_name',
                    self::FIELD_LAST_NAME      => 'last_name',
                    self::FIELD_USERNAME       => 'name',
                    self::FIELD_EMAILS         => 'emails'
                ]),
            self::getDefaultLoadersMap()
                ->loader('emails')->readdFields([self::FIELD_EMAILS])
        );
    }

    protected function profileLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }

    protected function emailsLoader()
    {
        return $this->service->requestJSON(self::REQUEST_PROFILE);
    }


    protected function emailNormalizer()
    {
        $emails = $this->getField(self::FIELD_EMAILS);

        if( !empty( $emails['preferred']) ) return $emails['preferred'];
        if( !empty( $emails['account']) ) return $emails['account'];
        if( !empty( $emails['personal']) ) return $emails['personal'];
        if( !empty( $emails['business']) ) return $emails['business'];
        if( !empty( $emails['other']) ) return $emails['other'];

        return NULL;
    }
}

