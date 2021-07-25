<?php

namespace Msa\Orm;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\UserTable;
use Bitrix\Main\ORM\Query\Join;
use CFile;

Loc::loadMessages(__FILE__);

class MessagesTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'msa_chat_messages';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            (new IntegerField('ID', []))
                ->configureTitle(Loc::getMessage('MESSAGES_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true),
            (new IntegerField('UF_USER_ID', []))
                ->configureTitle(Loc::getMessage('MESSAGES_ENTITY_UF_USER_ID_FIELD')),
            (new Reference(
                'USER',
                UserTable::class,
                Join::on('this.UF_USER_ID', 'ref.ID')
            ))
                ->configureTitle(Loc::getMessage('MESSAGES_ENTITY_USER_FIELD')),
            (new TextField('UF_MESSAGE', []))
                ->configureTitle(Loc::getMessage('MESSAGES_ENTITY_UF_MESSAGE_FIELD')),
            (new DateField('UF_DATE', [
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return $value->format("d.m.Y H:m");
                        }
                    );
                }
            ]))
                ->configureTitle(Loc::getMessage('MESSAGES_ENTITY_UF_DATE_FIELD')),
        ];
    }

    /**
     * Метод возвращает сообщение по его айди
     * @param int $id
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMessageById(int $id): array
    {
        $message = self::query()
            ->setSelect([
                'UF_MESSAGE',
                'UF_USER_ID',
                'USER_NAME' => 'USER.NAME',
                'USER_LAST_NAME' => 'USER.LAST_NAME',
                'USER_PHOTO' => 'USER.PERSONAL_PHOTO',
                'UF_DATE',
            ])
            ->addFilter('ID', $id)
            ->fetch();

        $message['USER_FULL_NAME'] = "{$message['USER_NAME']} {$message['USER_LAST_NAME']}";
        $message['USER_PHOTO_SRC'] = CFile::GetPath($message["USER_PHOTO"]);

        return $message;
    }

    /**
     * Метод возвращает все сообщения из таблицы, и добавляет путь к файлам аватарок авторов сообщений
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMessages(): array
    {
        $messages = self::query()
            ->setSelect([
                'UF_MESSAGE',
                'UF_USER_ID',
                'USER_NAME' => 'USER.NAME',
                'USER_LAST_NAME' => 'USER.LAST_NAME',
                'USER_PHOTO' => 'USER.PERSONAL_PHOTO',
                'UF_DATE',
            ])
            ->fetchAll();

        $photoIds = array_values(array_unique(array_column($messages, 'USER_PHOTO')));

        $photos =  CFile::GetList([], ['@ID' => $photoIds]);
        $userPhotos = [];

        while ($photo = $photos->fetch()) {
            $userPhotos[$photo['ID']] = CFile::GetFileSRC($photo);
        }

        foreach ($messages as &$message) {
            $message['USER_FULL_NAME'] = "{$message['USER_NAME']} {$message['USER_LAST_NAME']}";

            $message['USER_PHOTO_SRC'] = $userPhotos[$message["USER_PHOTO"]];
        }

        return $messages;
    }

}

