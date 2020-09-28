<?php

namespace Manao\AuthSms;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Security\Random;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\UserTable;

Loc::loadMessages(__FILE__);

/**
 * Class Manager
 * @package Ctweb\SMSAuth
 */
class Manager
{
	const STEP_PHONE_WAITING = 1;
	const STEP_USER_WAITING = 2;
	const STEP_CODE_WAITING = 3;
	const STEP_SUCCESS = 4;

	const STATE_NONE = 0;
	const STATE_AUTH = 1;
	const STATE_REGISTER = 2;

	const SESSION_FIELD_EXPIRE_TIME = 'EXPIRE';
	const SESSION_FIELD_STATE = 'STATE';
	const SESSION_FIELD_STEP = 'STEP';
	const SESSION_FIELD_CODE = 'CODE';
	const SESSION_FIELD_USER_ID = 'USER_ID';

	const GEN_PASSWORD_LENGTH = 11;

	protected $client;
	private $options;

	public function __construct()
	{
		$this->options = Module::getOptions();
	}

	public function getStep()
	{
		return $_SESSION[self::class][self::SESSION_FIELD_STEP];
	}

	public function getExpireTime()
	{
		return !$this->isTimeExpired() ? $_SESSION[self::class][self::SESSION_FIELD_EXPIRE_TIME] : false;
	}

	public function isTimeExpired()
	{
		return (isset($_SESSION[self::class][self::SESSION_FIELD_EXPIRE_TIME]) && time() > $_SESSION[self::class][self::SESSION_FIELD_EXPIRE_TIME]);
	}

	public function StartUserAuth($user_id)
	{
		$phone_field = $this->options['PHONE_FIELD'];

		if ($phone_field) {
			$arSelect = array("ID", 'ACTIVE');
			if (Module::CoreHasOwnPhoneAuth() && $phone_field === 'PHONE_NUMBER') {
				$arSelect[$phone_field] = 'PHONE.' . $phone_field;

				$arUser = UserTable::getList(array(
					'filter' => array(
						"ID" => $user_id
					),
					'select' => $arSelect,
					'runtime' => array(
						'PHONE' => array(
							'data_type' => 'Bitrix\Main\UserPhoneAuthTable',
							'reference' => array(
								'ref.USER_ID' => 'this.ID',
							),
							'join_type' => 'left'
						)
					)
				))->fetch();
			} else {
				$arSelect[] = $phone_field;

				$arUser = UserTable::getList(array(
					'filter' => array(
						"ID" => $user_id
					),
					'select' => $arSelect,
				))->fetch();
			}

			if ($arUser && $arUser['ID'] == $user_id && strlen($arUser[$phone_field])) {
				$_SESSION[self::class][self::SESSION_FIELD_USER_ID] = $arUser['ID'];
				$res = $this->SendAuthCode($arUser[$phone_field]);
				if ($res) {
					$this->setState(self::STATE_AUTH);
					$this->setStep(self::STEP_CODE_WAITING);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @param $phone
	 * @return bool
	 */
	private function SendAuthCode($phone)
	{
		$phone = $this->NormalizePhone($phone);
		$minlength = $this->options['MIN_PHONE_LENGTH'];

		if (strlen($phone) < $minlength)
			return false;

		$code = $this->GenerateCode();

		$timeExpire = intval($this->options['TIME_EXPIRE']);

		$_SESSION[self::class][self::SESSION_FIELD_CODE] = md5($code);
		$_SESSION[self::class][self::SESSION_FIELD_EXPIRE_TIME] = time() + $timeExpire;

		if ($this->options['DEBUG']) {
			Module::addLog(Loc::getMessage('CW_TEST_MESSAGE_SENDED', array('#PHONE#' => $phone, '#CODE#' => $code)), 'MESSAGE');
			return true;
		}
		return (bool)$this->SendSMS($phone, $code);
	}

	/**
	 * @emits OnNormalizePhone ( string $original, &string $normalized )
	 * @param string $phone
	 * @return string
	 */
	public function NormalizePhone($phone)
	{
		$digits =  str_split(preg_replace("/[^\d]/", "", (string) $phone));
		if (count($digits) === 11 && $digits[0] == '8')
			$digits[0] = '7';
		elseif (count($digits) === 10)
			array_unshift($digits, '7');

		$result = '+' . join('',$digits);

		$event = new \Bitrix\Main\Event(Module::MODULE_ID, "OnNormalizePhone", array($phone, &$result));
		$event->send();

		if (!is_string($result))
			throw new \InvalidArgumentException("Normalized phone should be a string!");

		return $result;
	}

	public function GenerateCode()
	{
		$length = intval($this->options['CODE_LENGTH']);
		$alphabet = $this->options['ALPHABET'];
		if (strlen($alphabet)) {
			return strtoupper(Random::getStringByCharsets($length, $alphabet));
		} else {
			return strtoupper(Random::getString($length));
		}
	}

	private function SendSMS($phone, $code)
	{
		if ($this->options['ACTIVE']) {
			$phone = $this->NormalizePhone($phone);
			$obProvider = null;
			try {
				$obProvider = Module::getProvider($this->options['PROVIDER']);

				$arFields = array(
					"CODE" => $code,
					"PHONE" => $phone
				);

				$event = new \Bitrix\Main\Event(Module::MODULE_ID, "OnSendSMS", array(&$arFields, &$obProvider));
				$event->send();

				$success = $obProvider->sendSMS($arFields);

				$event = new \Bitrix\Main\Event(Module::MODULE_ID, "OnAfterSendSMS", array($success, $arFields, $obProvider));
				$event->send();

				return true;
			} catch (\Exception $e) {
				Module::addLog($e->getMessage(), 'ERROR');
				return false;
			}
		}
		return null;
	}

	protected function setState($state)
	{
		$_SESSION[self::class][self::SESSION_FIELD_STATE] = $state;
	}

	public function setStep($step = self::STEP_PHONE_WAITING)
	{
		$_SESSION[self::class][self::SESSION_FIELD_STEP] = $step;
	}



	public function StartUserRegister($arFields)
	{
		$arResult = array();

		$phoneField = $this->options['PHONE_FIELD'];
		$arFields[$phoneField] = $this->NormalizePhone($arFields[$phoneField]);
		$minlength = $this->options['MIN_PHONE_LENGTH'];

		// check phone length
		if (strlen($arFields[$phoneField]) < $minlength) {
			$arResult['ERRORS'][] = 'PHONE_SHORT_LENGTH';
		}

		$isAllowedRegisterAuth = true;

		// check user exists (even not active)
		$arUsers = $this->GetUsersByPhone($arFields[$phoneField], true);
		if ($arUsers) {
			$user = reset($arUsers);
			if ($user['ACTIVE'] === 'N' && !$user['LAST_LOGIN']) {
				// Delete non-confirmed user for new registration try
				\CUser::Delete($user['ID']);
			} elseif ($isAllowedRegisterAuth) {
				// Set authorize step if user with phone is found
				$this->setState(self::STATE_AUTH);
			} else {
				$arResult['ERRORS'][] = 'PHONE_USER_EXISTS';
			}
		}

		if (empty($arResult['ERRORS'])) {

			if ($this->getState() === self::STATE_AUTH) {
				$_SESSION[self::class][self::SESSION_FIELD_USER_ID] = $user['ID'];
				$res = $this->SendAuthCode($user['PHONE']);
				if ($res) {
					$this->setStep(self::STEP_CODE_WAITING);
				} else {
					$arResult['ERRORS'][] = $res;
				}
			} else {
				$timestamp = time();

				if (!trim($arFields['EMAIL'])) {
					switch ($this->options['NEW_EMAIL_AS']) {
						case "PHONE":
							$arFields['EMAIL'] = "{$arFields[$phoneField]}@noemail.sms";
							break;
						default:
							$arFields['EMAIL'] = "{$timestamp}@noemail.sms";
					}
				}

				if (!trim($arFields['LOGIN'])) {
					switch ($this->options['NEW_LOGIN_AS']) {
						case "EMAIL":
							$arFields['LOGIN'] = $arFields['EMAIL'];
							break;
						case "PHONE":
							$arFields['LOGIN'] = $arFields[$phoneField];
							break;
						default:
							$arFields['LOGIN'] = "user_{$timestamp}";
					}
				}

				if ($this->options['NEW_LOGIN_AS'])

					$arFields['PASSWORD'] = Random::getString(self::GEN_PASSWORD_LENGTH);
				$arFields['CONFIRM_PASSWORD'] = $arFields['PASSWORD'];

				$arFields['ACTIVE'] = 'N';

				$groups = Option::get('main', 'new_user_registration_def_group', false);
				if ($groups) {
					$groups = explode(',', $groups);
					$arFields['GROUP_ID'] = $groups;
				}

				$user = new \CUser;
				if ($user_id = $user->Add($arFields)) {
					$_SESSION[self::class][self::SESSION_FIELD_USER_ID] = $user_id;
					$res = $this->SendAuthCode($arFields[$phoneField]);
					if ($res) {
						$this->setState(self::STATE_REGISTER);
						$this->setStep(self::STEP_CODE_WAITING);
					} else {
						$arResult['ERRORS'][] = $res;
					}
				} else {
					$arResult['ERRORS'][] = $user->LAST_ERROR;
				}
			}
		}
		return $arResult;
	}

	public function isTimeStarted()
	{
		return isset($_SESSION[self::class][self::SESSION_FIELD_EXPIRE_TIME]);
	}

	public function GetUsersByPhone($phone, $all = false)
	{
		$arResult = array();
		$phone = $this->NormalizePhone($phone);
		$minlength = $this->options['MIN_PHONE_LENGTH'];

		if (strlen($phone) < $minlength)
			return false;

		$phone_field = $this->options['PHONE_FIELD'];
		if ($phone_field) {
			$arFilter = array();
			$arSelect = array("ID", "ACTIVE", "LAST_LOGIN", "LOGIN", "LAST_NAME", "NAME");
			if (Module::CoreHasOwnPhoneAuth() && $phone_field === 'PHONE_NUMBER') {
				$arFilter['%PHONE.' . $phone_field] = $phone;
				$arSelect[$phone_field] = 'PHONE.' . $phone_field;

				if (!$all) {
					$arFilter['ACTIVE'] = 'Y';
				}

				$users = UserTable::getList(array(
					'filter' => $arFilter,
					'select' => $arSelect,
					'runtime' => array(
						'PHONE' => array(
							'data_type' => 'Bitrix\Main\UserPhoneAuthTable',
							'reference' => array(
								'ref.USER_ID' => 'this.ID',
							),
							'join_type' => 'left'
						)
					)
				));
			} else {
				$arFilter['%' . $phone_field] = $phone;
				$arSelect[] = $phone_field;

				if (!$all) {
					$arFilter['ACTIVE'] = 'Y';
				}

				$users = UserTable::getList(array(
					'filter' => $arFilter,
					'select' => $arSelect,
				));
			}

			while ($u = $users->Fetch()) {
				$user_phone = $this->NormalizePhone($u[$phone_field]);
				if ($user_phone === $phone) {
					$arResult[] = array(
						"ID" => $u["ID"],
						"ACTIVE" => $u["ACTIVE"],
						"LAST_LOGIN" => $u["LAST_LOGIN"],
						"LAST_NAME" => $u["LAST_NAME"],
						"NAME" => $u["NAME"],
						"LOGIN" => $u["LOGIN"],
						"PHONE" => $u[$phone_field]
					);
				}
			}
		}

		if (empty($arResult))
			return false;
		else
			return $arResult;
	}

	protected function getState()
	{
		return $_SESSION[self::class][self::SESSION_FIELD_STATE];

	}

	/**
	 * @emits OnAfterRegisterConfirm ( int )
	 * @param $code
	 * @return bool
	 */
	public function RegisterByCode($code)
	{
		global $USER;

		if ($this->getState() === self::STATE_AUTH) {
			return $this->AuthByCode($code);
		}

		$code = strtoupper($code);

		if ($this->isTimeStarted() && !$this->isTimeExpired()) {
			if (md5($code) === $_SESSION[self::class][self::SESSION_FIELD_CODE] && $_SESSION[self::class][self::SESSION_FIELD_USER_ID] > 0) {
				$user = new \CUser;
				$user->Update($_SESSION[self::class][self::SESSION_FIELD_USER_ID], array('ACTIVE' => 'Y'));
				$USER->Authorize($_SESSION[self::class][self::SESSION_FIELD_USER_ID]);
				$this->clearSession();
				$this->setStep(self::STEP_SUCCESS);

				$event = new \Bitrix\Main\Event(Module::MODULE_ID, "OnAfterRegisterConfirm", array($_SESSION[self::class][self::SESSION_FIELD_USER_ID]));
				$event->send();

				return true;
			}
		}

		return false;
	}

	public function AuthByCode($code, $save_session = null)
	{
		global $USER;

		$code = strtoupper($code);

		if (!$this->isTimeExpired()) {
			if (md5($code) === $_SESSION[self::class][self::SESSION_FIELD_CODE] && $_SESSION[self::class][self::SESSION_FIELD_USER_ID] > 0) {
				$USER->Authorize($_SESSION[self::class][self::SESSION_FIELD_USER_ID], $save_session === 'Y');
				$this->setStep(self::STEP_SUCCESS);
				$this->clearSession();
				return true;
			}
			return false;
		} else {
			return false;
		}
	}

	public function clearSession()
	{
		unset($_SESSION[self::class]);
	}

	public function getSessionParams()
	{
		return $_SESSION[self::class];
	}

	public function AbortUserRegister()
	{
		if ($_SESSION[self::class][self::SESSION_FIELD_USER_ID] > 0) {
			$user = \CUser::getByID($_SESSION[self::class][self::SESSION_FIELD_USER_ID])->Fetch();
			if ($user && $user['ACTIVE'] === 'N' && !$user['LAST_LOGIN']) {
				\CUser::Delete($user['ID']);
				$this->clearSession();
			}
		}
	}
}

?>
