<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\actions\objects;

use flipbox\ember\actions\model\traits\Manage;
use flipbox\ember\exceptions\RecordNotFoundException;
use flipbox\hubspot\actions\traits\ElementResolverTrait;
use flipbox\hubspot\actions\traits\FieldResolverTrait;
use flipbox\hubspot\records\ObjectAssociation;
use yii\base\Action;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since  1.0.0
 */
abstract class AbstractAssociationAction extends Action
{
    use Manage,
        FieldResolverTrait,
        ElementResolverTrait;

    /**
     * @param Model $model
     * @return bool
     * @throws RecordNotFoundException
     */
    protected function ensureAssociation(Model $model): bool
    {
        if (!$model instanceof ObjectAssociation) {
            throw new RecordNotFoundException(sprintf(
                "HubSpot Resource Association must be an instance of '%s', '%s' given.",
                ObjectAssociation::class,
                get_class($model)
            ));
        }

        return true;
    }
}
