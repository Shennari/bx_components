<?php

namespace Manao\AuthSms;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

\CModule::AddAutoloadClasses('manao.authfromsms', array(
    '\Manao\AuthSms\Manager' => 'lib/manager.php',
    '\Manao\AuthSms\ProviderBase' => 'lib/provider.base.php',
    '\Manao\AuthSms\CAdminForm' => 'lib/cadminform.php',
));
require_once(__DIR__ . '/vendor/autoload.php');

class Module
{
    const MODULE_ID = 'manao.authfromsms';
    const LOG_TYPE_NONE = 0;
    const LOG_TYPE_MESSAGES = 1;
    const LOG_TYPE_ERRORS = 2;
    const LOG_TYPE_ALL = 3;
    const LOG_FILE = __DIR__ . '/logs/module.log';

    static public function getDefaultOptions()
    {
        return array(
            'ACTIVE' => 0,
            'LOG_MESSAGES' => 2,
            'DEBUG' => 0,
            'PHONE_FIELD' => key(self::getPhoneFieldList()),
            'CODE_LENGTH' => 5,
            'ALPHABET' => '',
            'MIN_PHONE_LENGTH' => 5,
            'NEW_LOGIN_AS' => 'timestamp',
            'NEW_EMAIL_AS' => 'timestamp',
            'TIME_EXPIRE' => 180,
            'PROVIDER' => key(self::getProviderList()),
            'TRANSLIT' => 0,
            'ALLOW_REGISTER_AUTH' => 0,
            'REGISTER_FIELDS' => array(key(self::getPhoneFieldList())),
            'TEXT_MESSAGE' => "Код для авторизации: #CODE#",
            'NO_PHONE_ERRORS' => 0,
        );
    }

    static public function getOptions()
    {
        $options = Option::getForModule(self::MODULE_ID);
        $providers = array_merge(self::getDefaultOptions(), $options);
        foreach ($providers as $key => $value) {
            $value = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($value)) {
                $providers[$key] = $value;
            }
        }
        return $providers;
    }

    static public function getLogs()
    {
        if (!file_exists(self::LOG_FILE)) {
            if (!is_dir(dirname(self::LOG_FILE))) {
                mkdir(dirname(self::LOG_FILE), 0755, true);
            }
            touch(self::LOG_FILE);
        }
        $phoneList = file(self::LOG_FILE);
        return \array_map(function ($phone) {
            list($type, $timestamp, $text) = explode(' | ', $phone, 3);
            return ['TIMESTAMP' => $timestamp, 'TYPE' => $type, 'TEXT' => $text,];
        }, $phoneList);
    }

    static public function addLog($text, $type = 'MESSAGE')
    {
        $log_type = intval(Option::get(self::MODULE_ID, 'LOG_MESSAGES', self::LOG_TYPE_NONE));
        if ($type === 'ERROR' && in_array($log_type, array(
                self::LOG_TYPE_ERRORS,
                self::LOG_TYPE_ALL
            )) || $type === 'MESSAGE' && in_array($log_type,
                array(self::LOG_TYPE_MESSAGES, self::LOG_TYPE_ALL))) {
            file_put_contents(self::LOG_FILE,
                sprintf('%s | %s | %s', $type, date('d.m.Y H:i:s'),
                    $text . PHP_EOL), FILE_APPEND);
        }
    }

    static public function getProviderList()
    {
        $providers = array(
            'SMSRU' => array(
                'NAME' => 'sms.ru',
                'PATH' => __DIR__ . '/lib/providers/smsru.php',
                'CLASS' => '\Manao\AuthSms\ProviderSMSRU'
            ),
        );
        $event = new \Bitrix\Main\Event(self::MODULE_ID, 'OnGetProviderList', array(&$providers));
        $event->send();
        return $providers;
    }

    public static function CoreHasOwnPhoneAuth()
    {
        return (bool)CheckVersion(ModuleManager::getVersion('main'), '18.5.0');
    }

    static public function clearLog()
    {
        if (!file_exists(self::LOG_FILE)) {
            return;
        }
        unlink(self::LOG_FILE);
    }

    static public function getPhoneFieldList()
    {
        $phoneList = array();
        if (self::CoreHasOwnPhoneAuth()) {
            $phoneList['PHONE_NUMBER'] = Loc::getMessage('CW_REG_FIELD_PHONE_NUMBER');
        }
        $phoneList['PERSONAL_PHONE'] = Loc::GetMessage('FIELD_PERSONAL_PHONE');
        $phoneList['PERSONAL_FAX'] = Loc::GetMessage('FIELD_PERSONAL_FAX');
        $phoneList['PERSONAL_MOBILE'] = Loc::GetMessage('FIELD_PERSONAL_MOBILE');
        $phoneList['PERSONAL_PAGER'] = Loc::GetMessage('FIELD_PERSONAL_PAGER');
        $phoneList['WORK_PHONE'] = Loc::GetMessage('FIELD_WORK_PHONE');
        $phoneList['WORK_FAX'] = Loc::GetMessage('FIELD_WORK_FAX');
        $phoneList['WORK_PAGER'] = Loc::GetMessage('FIELD_WORK_PAGER');
        $event = new \Bitrix\Main\Event(self::MODULE_ID, 'OnGetPhoneFieldList', array(&$phoneList));
        $event->send();
        return $phoneList;
    }



    static public function updateOptions($post)
    {
        $default_options = self::getDefaultOptions();
        foreach ($default_options as $key => $value) {
            if ($key === 'PHONE_FIELD' && $value !== $post[$key]) {
                Option::set(self::MODULE_ID, 'NO_PHONE_ERRORS', 0);
            }
            if ($post[$key]) {
                $value = $post[$key];
            }
            if (is_array($value)) {
                $value = json_encode($value);
            }
            Option::set(self::MODULE_ID, $key, $value);
        }
        $provider = self::getProvider($post['PROVIDER']);
        if ($provider) {
            $provider->updateOptions($post);
        }
    }

    static public function getProvider($provider = null)
    {
        if (strlen($provider)) {
            $providers = self::getProviderList()[$provider];
            if (file_exists($providers['PATH'])) {
                require_once $providers['PATH'];
                if (class_exists($providers['CLASS'])) {
                    try {
                        $provider = new $providers['CLASS'];
                        return $provider;
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                } else {
                    throw new \Exception(Loc::getMessage('ERROR_CLASS_NOT_FOUND',
                        array('#CLASS#' => $providers['CLASS'])));
                }
            } else {
                throw new \Exception(Loc::getMessage('ERROR_PATH_NOT_FOUND',
                    array('#PATH#' => $providers['Path'])));
            }
        }
        return null;
    }

    static public function getNewLoginAsList()
    {
        return array(
            'TIMESTAMP' => Loc::getMessage("SWSA_NEW_LOGIN_AS_TIMESTAMP"),
            'EMAIL' => Loc::getMessage("SWSA_NEW_LOGIN_AS_EMAIL"),
            'PHONE' => Loc::getMessage("SWSA_NEW_LOGIN_AS_PHONE"),
        );
    }

    static public function getNewEmailAsList()
    {
        return array(
            'TIMESTAMP' => Loc::getMessage("SWSA_NEW_EMAIL_AS_TIMESTAMP"),
            'PHONE' => Loc::getMessage("SWSA_NEW_EMAIL_AS_PHONE"),
        );
    }

    static public function getLogOptions()
    {
        return array(
            self::LOG_TYPE_NONE => Loc::getMessage('LOG_TYPE_NONE'),
            self::LOG_TYPE_MESSAGES => Loc::getMessage('LOG_TYPE_MESSAGES'),
            self::LOG_TYPE_ERRORS => Loc::getMessage('LOG_TYPE_ERRORS'),
            self::LOG_TYPE_ALL => Loc::getMessage('LOG_TYPE_ALL'),
        );
    }

    static public function getUserRegisterFields()
    {
        $providers = array(
            'LOGIN' => Loc::getMessage('CW_REG_FIELD_LOGIN'),
            'NAME' => Loc::getMessage('CW_REG_FIELD_NAME'),
            'LAST_NAME' => Loc::getMessage('CW_REG_FIELD_LAST_NAME'),
            'SECOND_NAME' => Loc::getMessage('CW_REG_FIELD_SECOND_NAME'),
            'EMAIL' => Loc::getMessage('CW_REG_FIELD_EMAIL'),
            'PHONE_NUMBER' => Loc::getMessage('CW_REG_FIELD_PHONE_NUMBER'),
            'PERSONAL_PHONE' => Loc::getMessage('CW_REG_FIELD_PERSONAL_PHONE'),
            'PERSONAL_FAX' => Loc::getMessage('CW_REG_FIELD_PERSONAL_FAX'),
            'PERSONAL_MOBILE' => Loc::getMessage('CW_REG_FIELD_PERSONAL_MOBILE'),
            'PERSONAL_PAGER' => Loc::getMessage('CW_REG_FIELD_PERSONAL_PAGER'),
            'WORK_PHONE' => Loc::getMessage('CW_REG_FIELD_WORK_PHONE'),
            'WORK_FAX' => Loc::getMessage('CW_REG_FIELD_WORK_FAX'),
            'WORK_PAGER' => Loc::getMessage('CW_REG_FIELD_WORK_PAGER'),
        );
        $event = new \Bitrix\Main\Event(self::MODULE_ID, 'OnGetUserRegisterFields', array(&$providers));
        $event->send();
        return $providers;
    }
}