<?php

namespace flipbox\hubspot\services;

use Craft;
use craft\base\ElementInterface;
use craft\db\Query;
use flipbox\hubspot\records\Company as CompanyRecord;
use yii\base\Component;
use yii\base\Exception;

class Company extends Component
{

    /**
     * @param string $CompanyId
     * @return ElementInterface
     * @throws Exception
     */
    public function getElementByCompanyId(string $CompanyId)
    {
        if (!$element = $this->findElementByCompanyId($CompanyId)) {
            throw new Exception("Unable to get company");
        }

        return $element;
    }

    /**
     * @param string $CompanyId
     * @return ElementInterface|null
     */
    public function findElementByCompanyId(string $CompanyId)
    {
        if (!$elementId = (new Query())
            ->select(['elementId'])
            ->from([CompanyRecord::tableName()])
            ->where([
                'hubspotId' => $CompanyId
            ])
            ->scalar()) {
            return null;
        }

        return Craft::$app->getUsers()->getUserById($elementId);
    }

    /**
     * Find the Company Id by Element Id
     *
     * @param int $id
     * @return string|null
     */
    public function findCompanyIdByElementId(int $id)
    {
        $hubspotId = (new Query())
            ->select(['hubspotId'])
            ->from([CompanyRecord::tableName()])
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
                CompanyRecord::tableName(),
                [
                    'elementId' => $element->getId()
                ]
            )
            ->execute();

        return true;
    }

    /**
     * @param int $elementId
     * @param string $companyId
     * @return bool|array
     */
    public function associateByIds(
        int $elementId,
        string $companyId
    ) {

        $record = new CompanyRecord([
            'elementId' => $elementId,
            'hubspotId' => $companyId
        ]);

        if (!$record->save()) {
            return $record->getFirstErrors();
        }

        return true;
    }
}
