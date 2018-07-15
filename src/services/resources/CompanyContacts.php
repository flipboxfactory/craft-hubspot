<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\CompanyContactsAccessor;
use flipbox\hubspot\criteria\CompanyContactsMutator;
use flipbox\hubspot\criteria\CompanyContactsMutatorInterface;
use flipbox\hubspot\criteria\ObjectAccessorInterface;
use flipbox\hubspot\helpers\CacheHelper;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\services\resources\traits\ReadObjectTrait;
use flipbox\hubspot\transformers\collections\DynamicTransformerCollection;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use flipbox\hubspot\transformers\DynamicModelSuccess;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Contacts\Add;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Contacts\All;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Contacts\Remove;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CompanyContacts extends Component
{
    use ReadObjectTrait;

    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'companyContacts';

    /**
     * @inheritdoc
     */
    public static function defaultTransformer()
    {
        return [
            'class' => DynamicTransformerCollection::class,
            'handle' => self::HUBSPOT_RESOURCE,
            'transformers' => [
                TransformerCollectionInterface::SUCCESS_KEY => [
                    'class' => DynamicModelSuccess::class,
                    'resource' => self::HUBSPOT_RESOURCE
                ]
            ]
        ];
    }

    /**
     * @param array $config
     * @return ObjectAccessorInterface
     */
    public function getCriteria(array $config = []): ObjectAccessorInterface
    {
        return new CompanyContactsAccessor($config);
    }

    /**
     * @param array $config
     * @return CompanyContactsMutatorInterface
     */
    public function getBuilder(array $config = []): CompanyContactsMutatorInterface
    {
        return new CompanyContactsMutator($config);
    }

    /**
     * @inheritdoc
     */
    protected static function readRelayBuilderClass(): string
    {
        return All::class;
    }

    /**
     * @return string
     */
    protected static function addRelayBuilderClass(): string
    {
        return Add::class;
    }

    /**
     * @return string
     */
    protected static function removeRelayBuilderClass(): string
    {
        return Remove::class;
    }


    /*******************************************
     * ADD
     *******************************************/

    /**
     * @param CompanyContactsMutatorInterface $criteria
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function add(
        CompanyContactsMutatorInterface $criteria,
        array $extra = []
    ) {
        return $this->rawAdd(
            $criteria->getCompanyId(),
            $criteria->getContactId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawAdd(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawAddPipeline(
            $companyId,
            $contactId,
            $connection,
            $cache,
            $transformer
        )($extra);
    }

    /**
     * @param CompanyContactsMutatorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function addPipeline(
        CompanyContactsMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawAddPipeline(
            $criteria->getCompanyId(),
            $criteria->getContactId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawAddPipeline(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer),
            [
                'resource' => [static::addRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpAddRelay(
                $companyId,
                $contactId,
                ConnectionHelper::resolveConnection($connection),
                CacheHelper::resolveCache($cache)
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param CompanyContactsMutatorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpAddRelay(
        CompanyContactsMutatorInterface $criteria
    ): callable {
        return $this->rawHttpAddRelay(
            $criteria->getCompanyId(),
            $criteria->getContactId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpAddRelay(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $class = static::addRelayBuilderClass();

        /** @var RelayBuilderInterface $builder */
        $builder = new $class(
            $companyId,
            $contactId,
            ConnectionHelper::resolveConnection($connection),
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param CompanyContactsMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpAdd(
        CompanyContactsMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpAdd(
            $criteria->getCompanyId(),
            $criteria->getContactId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpAdd(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpAddRelay(
            $companyId,
            $contactId,
            $connection,
            $cache
        )();
    }


    /*******************************************
     * REMOVE
     *******************************************/

    /**
     * @param CompanyContactsMutatorInterface $criteria
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function remove(
        CompanyContactsMutatorInterface $criteria,
        array $extra = []
    ) {
        return $this->rawRemove(
            $criteria->getCompanyId(),
            $criteria->getContactId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawRemove(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawRemovePipeline(
            $companyId,
            $contactId,
            $connection,
            $cache,
            $transformer
        )($extra);
    }

    /**
     * @param CompanyContactsMutatorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function removePipeline(
        CompanyContactsMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawRemovePipeline(
            $criteria->getCompanyId(),
            $criteria->getContactId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawRemovePipeline(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer),
            [
                'resource' => [static::removeRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpAddRelay(
                $companyId,
                $contactId,
                ConnectionHelper::resolveConnection($connection),
                CacheHelper::resolveCache($cache)
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param CompanyContactsMutatorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRemoveRelay(
        CompanyContactsMutatorInterface $criteria
    ): callable {
        return $this->rawHttpRemoveRelay(
            $criteria->getCompanyId(),
            $criteria->getContactId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpRemoveRelay(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $class = static::removeRelayBuilderClass();

        /** @var RelayBuilderInterface $builder */
        $builder = new $class(
            $companyId,
            $contactId,
            ConnectionHelper::resolveConnection($connection),
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param CompanyContactsMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRemove(
        CompanyContactsMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpRemove(
            $criteria->getCompanyId(),
            $criteria->getContactId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpRemove(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpRemoveRelay(
            $companyId,
            $contactId,
            $connection,
            $cache
        )();
    }
}
