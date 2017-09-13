<?php

namespace flipbox\hubspot\services;

use Craft;
use craft\base\ElementInterface;
use craft\db\Query;
use flipbox\hubspot\records\Contact as ContactRecord;
use yii\base\Component;
use yii\base\Exception;

class Contact extends Component
{

    /**
     * @param string $ContactId
     * @return ElementInterface
     * @throws Exception
     */
    public function getElementByContactId(string $ContactId)
    {
        if (!$element = $this->findElementByContactId($ContactId)) {
            throw new Exception("Unable to get Contact");
        }

        return $element;
    }

    /**
     * @param string $ContactId
     * @return ElementInterface|null
     */
    public function findElementByContactId(string $ContactId)
    {
        if (!$elementId = (new Query())
            ->select(['elementId'])
            ->from([ContactRecord::tableName()])
            ->where([
                'hubspotId' => $ContactId
            ])
            ->scalar()) {
            return null;
        }

        return Craft::$app->getUsers()->getUserById($elementId);
    }

    /**
     * Find the Contact Id by Element Id
     *
     * @param int $id
     * @return string|null
     */
    public function findContactIdByElementId(int $id)
    {
        $hubspotId = (new Query())
            ->select(['hubspotId'])
            ->from([ContactRecord::tableName()])
            ->where([
                'elementId' => $id
            ])
            ->scalar();

        if (!$hubspotId) {
            return null;
        }

        return $hubspotId;
    }

    /**
     * @param ElementInterface $element
     * @return bool
     */
    public function disassociate(
        ElementInterface $element
    ) {
        Craft::$app->getDb()->createCommand()
            ->delete(
                ContactRecord::tableName(),
                [
                    'elementId' => $element->getId()
                ]
            )
            ->execute();

        return true;
    }

    /**
     * @param int $elementId
     * @param string $ContactId
     * @return bool|array
     */
    public function associateByIds(
        int $elementId,
        string $ContactId
    ) {

        $record = new ContactRecord([
            'elementId' => $elementId,
            'hubspotId' => $ContactId
        ]);

        if (!$record->save()) {
            return $record->getFirstErrors();
        }

        return true;
    }
}
