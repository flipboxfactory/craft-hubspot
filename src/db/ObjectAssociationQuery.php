<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\db;

use Craft;
use craft\db\QueryAbortedException;
use craft\helpers\Db;
use flipbox\craft\sortable\associations\db\SortableAssociationQuery;
use flipbox\craft\sortable\associations\db\traits\SiteAttribute;
use flipbox\ember\db\traits\ElementAttribute;
use flipbox\hubspot\records\ObjectAssociation;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method ObjectAssociation[] all()
 * @method ObjectAssociation one()
 */
class ObjectAssociationQuery extends SortableAssociationQuery
{
    use traits\FieldAttribute,
        traits\ObjectIdAttribute,
        ElementAttribute,
        SiteAttribute;

    /**
     * @inheritdoc
     */
    protected function fixedOrderColumn(): string
    {
        return ObjectAssociation::TARGET_ATTRIBUTE;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        Craft::configure(
            $this,
            $config
        );

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws QueryAbortedException if it can be determined that there won’t be any results
     */
    public function prepare($builder)
    {
        // Is the query already doomed?
        if (($this->field !== null && empty($this->field)) ||
            ($this->{ObjectAssociation::TARGET_ATTRIBUTE} !== null &&
                empty($this->{ObjectAssociation::TARGET_ATTRIBUTE})
            ) ||
            ($this->element !== null && empty($this->element))
        ) {
            throw new QueryAbortedException();
        }

        $this->applyConditions();
        $this->applySiteConditions();
        $this->applyObjectIdConditions();
        $this->applyFieldConditions();

        return parent::prepare($builder);
    }

    /**
     *  Apply query specific conditions
     */
    protected function applyConditions()
    {
        if ($this->element !== null) {
            $this->andWhere(Db::parseParam('elementId', $this->parseElementValue($this->element)));
        }
    }
}
