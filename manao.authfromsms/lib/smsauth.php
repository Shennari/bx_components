<?php
/*
namespace Ctweb\SMSAuth;

use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\Loader as Loader;
use Bitrix\Main\Config\Option as Option;

Loc::loadMessages(__FILE__);

class Manager
{
	protected $client;

	public static function SetAuthCode($phone) {
		$phone = preg_replace("/[^\d]/", "", $phone);

		if (strlen($phone) !== 11) return false;

		$code = self::GenerateCode();
		$_SESSION["SMS_AUTH"]["CODE"] = md5($code);
		$_SESSION["SMS_AUTH"]["EXPIRE"] = time() + intval(Option::get("manao.authfromsms", "CSR_TIME_EXPIRE", 3*60));

		if (Option::get("manao.authfromsms", "CSR_DEBUG") == 1) {
			return $code;
		}

		return self::SendSMS($phone, $code);
	}

	public static function SendSMS($phone, $code) {
        if(Option::get("manao.authfromsms", 'CSR_MODULE_ACTIVE') == 1){
            $phone = preg_replace("/[^\d]/", "", $phone);

            //���������� ���������� ����������
            $smsprovider = Option::get("manao.authfromsms", 'CSR_PROVIDER');
            spl_autoload_register(function ($smsprovider) {
				require_once(__DIR__.'/providers/'.$smsprovider . '.php');
            });
            $className = 'cwsa_'.$smsprovider;
            $provider = new $className("manao.authfromsms");

            $arFields = array(
            	"CODE" => $code,
				"PHONE" => $phone
			);
            $provider->sendSMS("manao.authfromsms", $arFields);
            return true;
        }
        return false;
	}

	public static function GenerateCode() {
		$dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		$length = intval(Option::get("manao.authfromsms", "CSR_CODE_LENGTH", 5));

		$salt = Option::get("manao.authfromsms", "CSR_CODE_SALT", "");
		$random = "";
		for ($i=0; $i<rand(0, strlen($dict)-1); $i++) {
            $random .= $dict[rand(0, strlen($dict)-1)];
		}

		$result = md5($salt.$random);
		$result = substr($result, rand(0, strlen($result)-$length-1), $length);
		$result = strtoupper($result);

		return $result;
	}
	
	public static function GetUserID($phone) {
		$phone = preg_replace("/[^\d]/i", "", $phone);
		if (!strlen($phone)) return false;
		if ($phone[0] === "7" || $phone[0] === "8")
			$phone = substr($phone, 1);

		
		$phone_field = Option::get("manao.authfromsms", "CSR_PHONE_FIELD", "PERSONAL_PHONE");

		$users = \CUser::GetList(($by="id"), ($sort = "asc"),
			array($phone_field => $phone),
			array("FIELDS" => array("ID", "LOGIN", "LAST_NAME", "NAME", $phone_field))
		);
		
		$arResult = array();
		while ($u = $users->Fetch()) {
			$user_phone = preg_replace("/[^\d]/i", "", $u[$phone_field]);
            if ($user_phone[0] === "7" || $user_phone[0] === "8")
                $user_phone = substr($user_phone, 1);

			if ($user_phone === $phone) {
				$arResult[] = array(
					"ID" => $u["ID"],
					"LAST_NAME" => $u["LAST_NAME"],
					"NAME" => $u["NAME"],
					"LOGIN" => $u["LOGIN"],
					"PHONE" => $u[$phone_field]
				);
			}
		}
		
		if (empty($arResult))
			return false;
		else
			return $arResult;
	}
}*/
?>
