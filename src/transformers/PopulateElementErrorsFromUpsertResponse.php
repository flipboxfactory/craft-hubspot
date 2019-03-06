<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/hubspot/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/hubspot
 */

namespace flipbox\craft\hubspot\transformers;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PopulateElementErrorsFromUpsertResponse
{
    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param ObjectsFieldInterface $field
     * @param string|null $id
     * @return ElementInterface
     */
    public function __invoke(
        ResponseInterface $response,
        ElementInterface $element,
        ObjectsFieldInterface $field,
        string $id = null
    ): ElementInterface {
        /** @var Element $element */

        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        $errors = call_user_func_array(
            new InterpretUpsertResponseErrors(),
            [
                $data
            ]
        );

        $element->addErrors($errors);

        return $element;
    }
}
