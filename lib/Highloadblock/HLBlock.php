<?php

namespace Msa\Highloadblock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Exception;
use CUserTypeEntity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class HLBlock
{
    const DEFAULT_HBLOCK_NAME = 'MsaChatMessages';
    const DEFAULT_HBLOCK_TABLE_NAME = 'msa_chat_messages';

    public function __construct()
    {
        if (!Loader::includeModule('highloadblock')) {
            throw new Exception(Loc::getMessage('HLBLOCK_NOT_LOADED'));
        }
    }

    /**
     * Метод созадет новый хайлоадблок
     * @param string $name
     * @param string $tableName
     * @return array|int
     * @throws \Bitrix\Main\SystemException
     */
    public function addHblock(string $name, string $tableName)
    {
        $fields = [
            'NAME' => $name,
            'TABLE_NAME' => $tableName,
        ];


        $result = HighloadBlockTable::add($fields);

        if ($result->isSuccess()) {
            return $result->getId();
        }

        $errorMessage = $result->getErrorMessages()[0] ?? Loc::getMessage('HLBLOCK_UNKNOWN_ERROR');

        throw new Exception($errorMessage);
    }

    /**
     * Возвращает айди хайлоадблока по его именти
     * @param $name
     * @return false|mixed|null
     */
    public function getHblockId($name)
    {
        try {
            $hlblock = HighloadBlockTable::getList(
                [
                    'select' => ['*'],
                    'filter' => ['NAME' => $name],
                ]
            )->fetch();

            return $hlblock["ID"] ?? null;
        } catch (Exception $e) {
            // TODO: обработать исключение
            return false;
        }
    }

    /**
     * Удаляет хайлоадблок по айди
     * @param $hlblockId
     * @return bool
     */
    public function destroyHblock($hlblockId)
    {
        $result = HighloadBlockTable::delete($hlblockId);

        return $result->isSuccess();
    }

    /**
     * Метод добавляет пользовательское поле хайлодблока
     * @param $hlblockId
     * @param $fieldName
     * @param $fields
     * @return false|int
     */
    public function addUserTypeEntity($hlblockId, $fieldName, $fields)
    {
        $default = [
            "ENTITY_ID"         => '',
            "FIELD_NAME"        => '',
            "USER_TYPE_ID"      => '',
            "XML_ID"            => '',
            "SORT"              => 500,
            "MULTIPLE"          => 'N',
            "MANDATORY"         => 'N',
            "SHOW_FILTER"       => 'I',
            "SHOW_IN_LIST"      => '',
            "EDIT_IN_LIST"      => '',
            "IS_SEARCHABLE"     => '',
            "SETTINGS"          => [],
            "EDIT_FORM_LABEL"   => ['ru' => '', 'en' => ''],
            "LIST_COLUMN_LABEL" => ['ru' => '', 'en' => ''],
            "LIST_FILTER_LABEL" => ['ru' => '', 'en' => ''],
            "ERROR_MESSAGE"     => '',
            "HELP_MESSAGE"      => '',
        ];

        $fields = array_replace_recursive($default, $fields);
        $fields['FIELD_NAME'] = $fieldName;
        $fields['ENTITY_ID'] = 'HLBLOCK_' . $hlblockId;

        $obUserField = new CUserTypeEntity;
        $userFieldId = $obUserField->Add($fields);

        return $userFieldId;
    }
}