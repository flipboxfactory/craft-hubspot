<?php

namespace flipbox\hubspot\services;

use Craft;
use craft\base\ElementInterface;
use craft\db\Query;
use flipbox\hubspot\records\ContactList as ContactListRecord;
use yii\base\Component;
use yii\base\Exception;

class ContactList extends Component
{

    /**
     * @param string $ContactListId
     * @return ElementInterface
     * @throws Exception
     */
    public function getElementByContactListId(string $ContactListId)
    {
        if (!$element = $this->findElementByContactListId($ContactListId)) {
            throw new Exception("Unable to get Contact List");
        }

        return $element;
    }

    /**
     * @param string $ContactListId
     * @return ElementInterface|null
     */
    public function findElementByContactListId(string $ContactListId)
    {
        if (!$elementId = (new Query())
            ->select(['elementId'])
            ->from([ContactListRecord::tableName()])
            ->where([
                'hubspotId' => $ContactListId
            ])
            ->scalar()) {
            return null;
        }

        return Craft::$app->getUsers()->getUserById($elementId);
    }

    /**
     * Find the Contact List Id by Element Id
     *
     * @param int $id
     * @return string|null
     */
    public function findContactListIdByElementId(int $id)
    {
        $hubspotId = (new Query())
            ->select(['hubspotId'])
            ->from([ContactListRecord::tableName()])
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
                ContactListRecord::tableName(),
                [
                    'elementId' => $element->getId()
                ]
            )
            ->execute();

        return true;
    }

    /**
     * @param int $elementId
     * @param string $ContactListId
     * @return bool|array
     */
    public function associateByIds(
        int $elementId,
        string $ContactListId
    ) {

        $record = new ContactListRecord([
            'elementId' => $elementId,
            'hubspotId' => $ContactListId
        ]);

        if (!$record->save()) {
            return $record->getFirstErrors();
        }

        return true;
    }
}
