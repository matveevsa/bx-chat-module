<?php

namespace Msa\Highloadblock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Exception;
use CUserTypeEntity;

class HLBlock
{
    const DEFAULT_HBLOCK_NAME = 'MsaChatMessages';
    const DEFAULT_HBLOCK_TABLE_NAME = 'msa_chat_messages';

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

        $errorMessage = $result->getErrorMessages()[0] ?? 'Неизвестаня ошибка';

        throw new Exception($errorMessage);
    }

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

    public function destroyHblock($hlblockId)
    {
        $result = HighloadBlockTable::delete($hlblockId);

        return $result->isSuccess();
    }

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