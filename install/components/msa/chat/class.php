<?php

use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Contract\Controllerable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

class chat extends CBitrixComponent implements Controllerable
{
    /**
     * @inheritdoc
     */
    public function configureActions(): array
    {
        $arPreFilters = [
            new Authentication(),
            new HttpMethod([HttpMethod::METHOD_POST]),
            new Csrf(),
        ];

        return [
            'addMessage' => [
                'prefilters' => $arPreFilters,
            ],
            'editMessage' => [
                'prefilters' => $arPreFilters,
            ],
            'getMessages' => [
                'prefilters' => $arPreFilters,
            ],
        ];
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}