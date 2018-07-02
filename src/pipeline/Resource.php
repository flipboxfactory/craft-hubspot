<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\pipeline;

use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\pipelines\HttpPipeline;
use flipbox\hubspot\pipeline\stages\TransformerCollectionStage;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Flipbox\Pipeline\Builders\BuilderTrait;
use Flipbox\Skeleton\Object\AbstractObject;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Log\LoggerInterface;

/**
 * A Relay pipeline builder intended to make building the Relay and Pipeline easier.
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method HttpPipeline build()
 */
class Resource extends AbstractObject implements PipelineBuilderInterface
{
    use BuilderTrait;

    /**
     * @var callable
     */
    protected $relay;

    /**
     * @var TransformerCollectionInterface|null
     */
    protected $transformer;

    /**
     * @param callable $relay
     * @param TransformerCollectionInterface|null $transformer
     * @param LoggerInterface|null $logger
     * @param array $config
     */
    public function __construct(
        callable $relay,
        TransformerCollectionInterface $transformer = null,
        LoggerInterface $logger = null,
        array $config = []
    ) {

        $this->setLogger($logger ?: HubSpot::getInstance()->getPsrLogger());
        $this->relay = $relay;
        $this->transformer = $transformer;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function createPipeline(array $config = []): HttpPipeline
    {
        $pipeline = new HttpPipeline(
            function () {
                return call_user_func($this->relay);
            },
            $this->createTransformerStage($this->transformer),
            $config
        );

        $pipeline->setLogger($this->getLogger());

        return $pipeline;
    }

    /**
     * @param null $source
     * @return mixed
     */
    public function execute($source = null)
    {
        // Resources do not pass a payload ... but they can pass a source, so that why this may look funny
        return call_user_func_array($this->build(), [null, $source]);
    }

    /**
     * @param mixed|null $source
     * @return mixed
     */
    public function __invoke($source = null)
    {
        return $this->execute($source);
    }

    /**
     * @param TransformerCollectionInterface|null $transformer
     * @return TransformerCollectionStage|null
     */
    private function createTransformerStage(
        TransformerCollectionInterface $transformer = null
    ) {
        if ($transformer === null) {
            return null;
        }

        return new TransformerCollectionStage($transformer);
    }
}
