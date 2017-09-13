<?php

namespace flipbox\hubspot\records;

use flipbox\spark\helpers\RecordHelper;
use flipbox\spark\records\RecordWithId;

abstract class AbstractObject extends RecordWithId
{
    /**
     * The table alias
     */
    const TABLE_ALIAS = 'hubspot';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'elementId'
                    ],
                    'number',
                    'integerOnly' => true
                ],
                [
                    [
                        'elementId',
                        'hubspotId',
                    ],
                    'required'
                ],
                [
                    'hubspotId',
                    'unique',
                    'targetAttribute' => [
                        'elementId',
                        'hubspotId'
                    ]
                ],
                [
                    [
                        'elementId',
                        'hubspotId'
                    ],
                    'safe',
                    'on' => [
                        RecordHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}
