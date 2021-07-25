<?php

use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Msa\Highloadblock\HLBlock;
use Msa\Orm\MessagesTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
if (!Loader::includeModule('msa.custom.chat')) {
    echo "Модуль Чата не подключен";
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
        ];
    }

    /**
     * @inheritdoc
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['HBLOCK_NAME'] = $arParams['HBLOCK_NAME'] ?? HLBlock::DEFAULT_HBLOCK_NAME;

        $arParams['FULLSCREEN'] = $arParams['FULLSCREEN'] === 'Y' ?: 'N';

        return $arParams;
    }

    protected function getMessages(): array
    {
        return MessagesTable::getMessages();
    }

    /**
     * Экшен добавляет сообщение
     * @param int $userId
     * @param string $message
     * @return array
     * @throws Exception
     */
    public function addMessageAction(int $userId, string $message): array
    {
        $data = [
            'UF_USER_ID' => $userId,
            'UF_MESSAGE' => $message,
            'UF_DATE' => new DateTime(),
        ];

        $result = MessagesTable::add($data);


        if ($result->isSuccess()) {
            $message = MessagesTable::getMessageById($result->getId());
            return $message;
        }

        return $result->getErrorMessages();
    }

    /**
     * Экшен обновляет сообщение
     * @param int $messageId
     * @param string $message
     * @return array
     * @throws Exception
     */
    public function editMessageAction(int $messageId, string $message): array
    {
        $data = [
            'UF_MESSAGE' => $message,
            'UF_DATE' => new DateTime(),
        ];

        $result = MessagesTable::update($messageId, $data);


        if ($result->isSuccess()) {
            $message = MessagesTable::getMessageById($result->getId());
            return $message;
        }

        return $result->getErrorMessages();
    }


    public function executeComponent()
    {
        $this->arResult['MESSAGES'] = $this->getMessages();

        $this->includeComponentTemplate();
    }
}