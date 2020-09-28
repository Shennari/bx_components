<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class manao_authfromsms extends CModule
{
    //const MODULE_ID = "manao.authfromsms";

    function __construct() {
        if(file_exists(__DIR__."/version.php")) {
            $arModuleVersion = array();

            include(__DIR__."/version.php");
            $this->MODULE_ID = 'manao.authfromsms';
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME = Loc::getMessage("SMS_AUTH_MODULE_NAME");
            $this->MODULE_DESCRIPTION = Loc::getMessage("SMS_AUTH_MODULE_DESC");
            $this->PARTNER_NAME = 'Manao';
            $this->PARTNER_URI = 'https://manao-team.com';
        }

    }

    function DoInstall()
    {
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        $this->installFiles();
        return true;
    }

    function DoUninstall() {
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
        Option::delete($this->MODULE_ID);
        return true;
    }

    function installFiles()
    {
        //Путь до папки /install/components в модуле
        $pathComponents = __DIR__ . "/components";

        //Проверяем сущетвует ли папка
        if(\Bitrix\Main\IO\Directory::isDirectoryExists($pathComponents))
            CopyDirFiles($pathComponents, \Bitrix\Main\Application::getDocumentRoot() . "/local/components", true, true);
        else
            throw new \Bitrix\Main\IO\InvalidPathException($pathComponents);

        return true;
    }

    function UnInstallFiles(){
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/local/components/' . $this->MODULE_ID . '/');
        return true;
    }



}
