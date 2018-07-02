<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\pipeline\pipelines;

use Flipbox\Pipeline\Pipelines\Pipeline;
use Flipbox\Pipeline\Processors\Processor;
use Flipbox\Skeleton\Logger\AutoLoggerTrait;

/**
 * A Relay pipeline which only accepts a Relay Segment and Pipeline.  The intent is to run
 * the Relay Segment and use the pipeline to handle any additional processing (such as transformations, etc).
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method Processor getProcessor()
 */
class HttpPipeline extends Pipeline
{
    use AutoLoggerTrait;

    /**
     * @var callable
     */
    private $relay;

    /**
     * @var callable
     */
    private $transformer;

    /**
     * @inheritdoc
     */
    protected $processor = Processor::class;

    /**
     * @param callable $relay
     * @param callable $transformer
     * @param array $config
     */
    public function __construct(
        callable $relay,
        callable $transformer = null,
        $config = []
    ) {
        $this->relay = $relay;
        $this->transformer = $transformer;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function process($payload = null, $source = null)
    {
        $stages = array_merge(
            array_filter([$this->relay, $this->transformer]),
            $this->getStages()
        );

        return $this->getProcessor()->process($stages, $payload, $source);
    }
}
