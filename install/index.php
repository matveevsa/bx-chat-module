<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

require_once(dirname(__DIR__) . "/lib/Highloadblock/HLBlock.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Msa\Highloadblock\HLBlock;

class msa_custom_chat extends CModule
{
    var $MODULE_ID = 'msa.custom.chat';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = 'Y';
    var $MODULE_PATH;

    function __construct()
    {
        if (!Loader::includeModule('highloadblock')) {
            throw new Exception('Модуль highloadblock не подключен');
        }

        $arModuleVersion = [];

        $this->MODULE_PATH = $this->getModulePath();
        include $this->MODULE_PATH.'/install/version.php';

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        //TODO: вынести все в лэнги
        $this->MODULE_NAME = "Модуль чата";
        $this->MODULE_DESCRIPTION = "После установки модуля будет доступен компонент msa:chat";
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->addHBlockMessages();

        RegisterModule($this->MODULE_ID);
        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->destroyHBlockMessages();

        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/{$this->MODULE_ID}/install/components",
            $_SERVER["DOCUMENT_ROOT"]."/local/components/", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/local/components/msa/chat");
        return true;
    }

    /**
     * Return path module
     *
     * @return string
     */
    protected function getModulePath()
    {
        $modulePath = explode('/', __FILE__);
        $modulePath = array_slice(
            $modulePath,
            0,
            array_search($this->MODULE_ID, $modulePath) + 1
        );

        return implode('/', $modulePath);
    }

    protected function addHBlockMessages()
    {
        $hblockHelper = new HLBlock();

        try {
            $id = $hblockHelper->addHblock(HLBlock::DEFAULT_HBLOCK_NAME, HLBlock::DEFAULT_HBLOCK_TABLE_NAME);

            $hblockHelper->addUserTypeEntity($id, 'UF_USER_ID', ['USER_TYPE_ID' => 'string']);
            $hblockHelper->addUserTypeEntity($id, 'UF_MESSAGE', ['USER_TYPE_ID' => 'string']);
            $hblockHelper->addUserTypeEntity($id, 'UF_DATE', ['USER_TYPE_ID' => 'date']);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function destroyHBlockMessages()
    {
        $hblockHelper = new HLBlock();

        try {
            $id = $hblockHelper->getHblockId(HLBlock::DEFAULT_HBLOCK_NAME);

            $hblockHelper->destroyHblock($id);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
