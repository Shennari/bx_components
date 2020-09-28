<?php


use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Manao\AuthSms\Manager;
use Manao\AuthSms\Module;

class SmsAuthComponent extends CBitrixComponent
{
    const ERROR_CODE_EMPTY = 'CODE_EMPTY';
    const ERROR_CODE_NOT_CORRECT = 'Неверный код';
    const ERROR_TIME_EXPIRED = 'TIME_EXPIRED';
    const ERROR_USER_NOT_FOUND = 'USER_NOT_FOUND';
    const ERROR_USER_NOT_CHOOSED = 'USER_NOT_CHOOSED';
    const ERROR_CAPTCHA_WRONG = 'CAPTCHA_WRONG';
    const ERROR_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    const RESULT_SUCCESS = 'SUCCESS';
    const RESULT_FAILED = 'FAILED';

    protected $manager;
    protected $moduleOptions;
    protected $context;
    protected $errors;

    function __construct($component = null)
    {
        parent::__construct($component);

        try {
            Loader::includeModule('manao.authfromsms');
        } catch (LoaderException $e) {
            $this->arResult['ERRORS'][] = $e->getMessage();
            ShowError($e->getMessage());
        }
        $this->manager = new Manager();
        $this->moduleOptions = Module::getOptions();
        $this->context = \Bitrix\Main\Context::getCurrent();
        $this->errors = [];
    }

    function onPrepareComponentParams($arParams)
    {
        global $APPLICATION; //переделать капчу

        $arParams['USE_CAPTCHA'] = Option::get('main', 'captcha_registration', 'N') == "Y" ? "Y" : 'N';
        if ($arParams['USE_CAPTCHA'] == 'Y') {
            $arParams['CAPTCHA_CODE'] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
        }

        return $arParams;
    }

    protected function setSessionField($key, $value)
    {
        $_SESSION[self::class][$key] = $value;
    }

    protected function getSessionField($key)
    {
        return $_SESSION[self::class][$key];
    }

    function executeComponent()
    {
        CJSCore::Init(['jquery']);
        global $APPLICATION;
        $this->setFrameMode(false);
        $this->arParams['MODULE_OPTIONS'] = $this->moduleOptions;
        $this->user = new CUser();

        if (!$this->user->isAuthorized()) {
            switch ($this->manager->getStep()) {
                case Manager::STEP_SUCCESS : // all ok, redirect waiting
                    $this->actionStepSuccess();
                    break;

                case Manager::STEP_USER_WAITING :
                    //$this->actionStepUserWaiting();
                    break;

                case Manager::STEP_CODE_WAITING : // user found, code waiting for auth
                    $this->actionStepCodeWaiting();
                    break;

                case Manager::STEP_PHONE_WAITING: // no action, phone waiting
                default: // no action, phone waiting
                    //$this->manager->setStep($this->manager::STEP_PHONE_WAITING);
                    $this->actionStepPhoneWaiting();
            }

            //$this->arResult['ERRORS'] = array_merge($this->arResult['ERRORS'], $this->getSessionField('ERRORS'));
            $this->arResult['USER_VALUES']['SAVE_SESSION'] = $this->getSessionField('SAVE_SESSION');
            $this->arResult['USER_VALUES']['PHONE'] = $this->getSessionField('PHONE');
            $this->arResult['EXPIRE_TIME'] = $this->manager->getExpireTime();
            ($this->arResult['STEP'] = $this->manager->getStep()) || ($this->arResult['STEP'] = Manager::STEP_PHONE_WAITING);
        } else {
            $this->arResult['AUTH_RESULT'] = self::RESULT_SUCCESS;
            $this->manager->clearSession();
            //header("Refresh:1; url=".$APPLICATION->GetCurPageParam());
        }


        $this->arParams['SESSION'] = $this->manager->getSessionParams();

        if ($this->request->isAjaxRequest()) {
            //$APPLICATION->RestartBuffer();
            /*echo \Bitrix\Main\Web\Json::encode([
                'errors' => $this->arResult['ERRORS'],
                'step' => $this->arResult['STEP'],
                'result' => $this->arResult['AUTH_RESULT'],
                'full' => $this->arResult
            ]);*/
            $this->includeComponentTemplate();
            $APPLICATION->FinalActions();
            die();
        }
        $this->includeComponentTemplate();
    }

    private function actionStepSuccess() {
        $this->arResult['AUTH_RESULT'] = self::RESULT_SUCCESS;
        $this->manager->clearSession();
        $this->arResult['REFRESH_PAGE'] = "Y";
        global $APPLICATION;
        header("Refresh:1; url=".$APPLICATION->GetCurPageParam());
    }

    private function actionStepCodeWaiting() {
        global $APPLICATION;
        if ($this->manager->isTimeExpired()) {
            $this->arResult['ERRORS'][] = self::ERROR_TIME_EXPIRED;
            $this->manager->clearSession();
        } else {
            if ($this->isPost()) {
                if (strlen($this->request->get('CODE'))) {
                    if (!$this->manager->RegisterByCode($this->request->get('CODE'), $this->getSessionField('SAVE_SESSION'))) {
                        $this->arResult['AUTH_RESULT'] = self::RESULT_FAILED;
                        $this->arResult['ERRORS'][] = self::ERROR_CODE_NOT_CORRECT;
                        $this->errors[] = self::ERROR_CODE_NOT_CORRECT;
                    } else {
                        $this->actionStepSuccess();

                    }
                } else {
                    $this->arResult['ERRORS'][] = self::ERROR_CODE_EMPTY;
                }
                $this->setSessionField('ERRORS', $this->arResult['ERRORS']);
            }
        }
    }

    private function actionStepPhoneWaiting() {
        global $APPLICATION;
        if ($this->isPost()) {
            // check captcha
            if ($this->arResult["USE_CAPTCHA"] == "Y")
                if (!$APPLICATION->CaptchaCheckCode($this->request->get("captcha_word"), $this->request->get("captcha_sid")))
                    $this->arResult['ERRORS'][] = self::ERROR_CAPTCHA_WRONG;

            if (strlen($this->request->get('PHONE')))
                $this->setSessionField('PHONE', $this->request->get('PHONE'));

            if (strlen($this->request->get('SAVE_SESSION')))
                $this->setSessionField('SAVE_SESSION', $this->request->get('SAVE_SESSION'));

            if (empty($this->arResult['ERRORS'])) {
                if ($this->getSessionField('PHONE')) {
                    $arUsers = $this->manager->GetUsersByPhone($this->getSessionField('PHONE'));
                    if ($arUsers) {
                        if ($this->arParams['ALLOW_MULTIPLE_USERS'] === 'Y' && count($arUsers) > 1) {
                            $this->manager->setStep(Manager::STEP_USER_WAITING);
                        } else {
                            $arUser = reset($arUsers);

                            if ($arUser['ID']) {
                                $res = $this->manager->StartUserAuth($arUser['ID']);
                                if (!$res) {
                                    $this->arResult['ERRORS'][] = self::ERROR_UNKNOWN_ERROR;
                                }
                            }
                        }
                    } else {
                        // register User
                        $errors = $this->manager->StartUserRegister([$this->moduleOptions['PHONE_FIELD'] => $this->getSessionField('PHONE')]);
                        $this->arResult['ERRORS'][] = $errors['ERRORS'];
                    }
                } else {
                    $this->clearSession();
                }
            }

            $this->setSessionField('ERRORS', $this->arResult['ERRORS']);
            LocalRedirect($APPLICATION->GetCurPageParam());
        } else {
            $this->arResult['ERRORS'][] = 'failed post security auth';
        }
    }

    private function isPost()
    {
        return $this->request->isPost() && check_bitrix_sessid();
    }
}