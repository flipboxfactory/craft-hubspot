<?php

namespace flipbox\hubspot\models;

use flipbox\hubspot\authentication\ApplicationKey;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\ApplicationPool;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\records\Company;
use flipbox\hubspot\records\Contact;
use yii\base\Model;

class Settings extends Model
{
    /**
     * @var bool
     */
    public $debugMode = false;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $userIdFieldHandle;

    /**
     * @var string
     */
    public $companyTableAlias = Company::TABLE_ALIAS;

    /**
     * @var string
     */
    public $contactTableAlias = Contact::TABLE_ALIAS;

    /**
     * @return AuthenticationStrategyInterface
     */
    public function getAuthenticationStrategy(): AuthenticationStrategyInterface
    {
        return new ApplicationKey();
    }

    /**
     * @return CacheStrategyInterface
     */
    public function getCacheStrategy(): CacheStrategyInterface
    {
        return new ApplicationPool();
    }
}
