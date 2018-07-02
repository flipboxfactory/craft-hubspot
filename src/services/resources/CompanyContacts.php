<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\builders\CompanyContactsBuilder;
use flipbox\hubspot\builders\CompanyContactsBuilderInterface;
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\CompanyContactsCriteria;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
use flipbox\hubspot\helpers\CacheHelper;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\services\resources\traits\ReadObjectTrait;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
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
     * @param array $config
     * @return ObjectCriteriaInterface
     */
    public function getCriteria(array $config = []): ObjectCriteriaInterface
    {
        return new CompanyContactsCriteria($config);
    }

    /**
     * @param array $config
     * @return CompanyContactsBuilderInterface
     */
    public function getBuilder(array $config = []): CompanyContactsBuilderInterface
    {
        return new CompanyContactsBuilder($config);
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
     * @param CompanyContactsBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function add(
        CompanyContactsBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawAdd(
            $builder->getCompanyId(),
            $builder->getContactId(),
            $connection,
            $cache,
            $transformer,
            $source
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
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawAdd(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawAddPipeline(
            $companyId,
            $contactId,
            $connection,
            $cache,
            $transformer
        )($source);
    }


    /**
     * @param CompanyContactsBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function addPipeline(
        CompanyContactsBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        return $this->rawAddPipeline(
            $builder->getCompanyId(),
            $builder->getContactId(),
            $connection,
            $cache,
            $transformer
        );
    }

    /**
     * @param CompanyContactsBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpAddRelay(
        CompanyContactsBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        return $this->rawHttpAddRelay(
            $builder->getCompanyId(),
            $builder->getContactId(),
            $connection,
            $cache
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
     * @param CompanyContactsBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpAdd(
        CompanyContactsBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpAdd(
            $builder->getCompanyId(),
            $builder->getContactId(),
            $connection,
            $cache
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
     * @param CompanyContactsBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function remove(
        CompanyContactsBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawRemove(
            $builder->getCompanyId(),
            $builder->getContactId(),
            $connection,
            $cache,
            $transformer,
            $source
        );
    }

    /**
     * @param string $companyId
     * @param string $contactId
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawRemove(
        string $companyId,
        string $contactId,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawRemovePipeline(
            $companyId,
            $contactId,
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param CompanyContactsBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function removePipeline(
        CompanyContactsBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        return $this->rawRemovePipeline(
            $builder->getCompanyId(),
            $builder->getContactId(),
            $connection,
            $cache,
            $transformer
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
     * @param CompanyContactsBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRemoveRelay(
        CompanyContactsBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        return $this->rawHttpRemoveRelay(
            $builder->getCompanyId(),
            $builder->getContactId(),
            $connection,
            $cache
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
     * @param CompanyContactsBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRemove(
        CompanyContactsBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpRemove(
            $builder->getCompanyId(),
            $builder->getContactId(),
            $connection,
            $cache
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
