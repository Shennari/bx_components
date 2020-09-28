<?
namespace Manao\AuthSms;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

Loc::loadMessages(__FILE__);

require_once('vendor/smsru/autoload.php');

class ProviderSMSRU extends ProviderBase {
    protected $code = 'SMSRU';

    public function Auth() {
        // Test: ok
        if (!$this->api) {
            $apiID = $this->getToken();
            $this->api = new \Zelenin\SmsRu\Api(new \Zelenin\SmsRu\Auth\ApiIdAuth($apiID));
        }

        return $this->api;
    }

	public function getBalance(){
        //Test: ok
		$client = $this->Auth();
		$balance = $client->myBalance();
		if($balance->code == 100){
			return $balance->balance;
		} else {
			$msg = $this->GetAttr($balance, 'availableDescriptions');
            return $msg[$balance->code];
		}
	}

	public static function getErrorCodes(){
		array(
			"-1"=>Loc::getMessage('PROVIDER_RESPONSE_-1'),
			"100"=>Loc::getMessage('PROVIDER_RESPONSE_100'),
			"101"=>Loc::getMessage('PROVIDER_RESPONSE_101'),
			"102"=>Loc::getMessage('PROVIDER_RESPONSE_102'),
			"103"=>Loc::getMessage('PROVIDER_RESPONSE_103'),
			"104"=>Loc::getMessage('PROVIDER_RESPONSE_104'),
			"105"=>Loc::getMessage('PROVIDER_RESPONSE_105'),
			"106"=>Loc::getMessage('PROVIDER_RESPONSE_106'),
			"107"=>Loc::getMessage('PROVIDER_RESPONSE_107'),
			"108"=>Loc::getMessage('PROVIDER_RESPONSE_108'),
			"130"=>Loc::getMessage('PROVIDER_RESPONSE_130'),
			"131"=>Loc::getMessage('PROVIDER_RESPONSE_131'),
			"132"=>Loc::getMessage('PROVIDER_RESPONSE_132'),
			"200"=>Loc::getMessage('PROVIDER_RESPONSE_200'),
		    "201"=>Loc::getMessage('PROVIDER_RESPONSE_201'),
		    "202"=>Loc::getMessage('PROVIDER_RESPONSE_202'),
		    "203"=>Loc::getMessage('PROVIDER_RESPONSE_203'),
		    "204"=>Loc::getMessage('PROVIDER_RESPONSE_204'),
		    "205"=>Loc::getMessage('PROVIDER_RESPONSE_205'),
		    "206"=>Loc::getMessage('PROVIDER_RESPONSE_206'),
		    "207"=>Loc::getMessage('PROVIDER_RESPONSE_207'),
		    "208"=>Loc::getMessage('PROVIDER_RESPONSE_208'),
		    "209"=>Loc::getMessage('PROVIDER_RESPONSE_209'),
			"210"=>Loc::getMessage('PROVIDER_RESPONSE_210'),
			"211"=>Loc::getMessage('PROVIDER_RESPONSE_211'),
			"220"=>Loc::getMessage('PROVIDER_RESPONSE_220'),
			"230"=>Loc::getMessage('PROVIDER_RESPONSE_230'),
			"231"=>Loc::getMessage('PROVIDER_RESPONSE_231'),
			"232"=>Loc::getMessage('PROVIDER_RESPONSE_232'),
			"300"=>Loc::getMessage('PROVIDER_RESPONSE_300'),
			"301"=>Loc::getMessage('PROVIDER_RESPONSE_301'),
			"302"=>Loc::getMessage('PROVIDER_RESPONSE_302'),
		);
	}

	public function sendSMS($arFields){
        //Test: ok
        if (strlen($arFields['PHONE']) <= 0) {
            $this->LAST_ERROR = Loc::getMessage('CSR_ERROR_NO_PHONE');
            return false;
        }

		$client = $this->Auth();

        $arSend = array();
        $arSend['message'] = $this->PrepareMessage($this->options['TEXT_MESSAGE'], $arFields);
        $arSend['phone'] = $arFields["PHONE"];

        // Prepare message
		$sms = new \Zelenin\SmsRu\Entity\Sms($arSend['phone'], $arSend['message']);

        if($this->options['TRANSLIT']){
            $sms->translit = 1;
        } else {
            $sms->translit = 0;
        }
        
        if($this->options[$this->getFieldKey('TEST_SMS')])
            $sms->test = 1;
        else
            $sms->test = 0;
		
		//Partner program
		$sms->partner_id=182951;
		
		// Sender
		$sedner = $this->options[$this->getFieldKey('SENDERS')];
		if(!empty($sedner)){
			$sms->from=$sedner;
		}
		
		//Sending SMS
		$res = $client->smsSend($sms);
		$status_code = $res->code;

		switch ($status_code) {
		    case 100:
		    case 101:
		    case 102:
		    case 103:
		        Module::addLog("{$arFields['PHONE']}: success");
		        return true;
		    default:
                Module::addLog("{$arFields['PHONE']}: {$this->getError($status_code)}", 'ERROR');
                $this->LAST_ERROR = $this->getError($status_code);
                return false;
		}
	}

	public function getSenders() {
        //Test: ok
        $arSenders = array();

        $token = $this->getToken();
        if ($token) {
	        $client = $this->Auth();
	        $response = $client->mySenders();

	        if ($response->code == 100) {
		        foreach ($response->phones as $sender) {
			        $arSenders[$sender] = $sender;
		        }
	        } else {
		        $this->LAST_ERROR = $this->getError($response->availableDescriptions[$response->code]);
	        }
        }

        return $arSenders;
    }

	public function showAuthForm($tabControl){
        //Test: ok
		$token = $this->getToken();
		$tabControl->AddEditField($this->getFieldKey('TOKEN'), GetMessage('CSR_API_KEY'), false, array("size"=>30, "maxlength"=>255), $token);
		if(!empty($token)){
			$balance = $this->getBalance();
			if(is_float($balance)){
				$tabControl->AddViewField($this->getFieldKey('BALANCE'), GetMessage("CSR_BALANCE"), $balance);
                $tabControl->AddCheckBoxField($this->getFieldKey('TEST_SMS'), GetMessage("CSR_TEST_SMS"), false, 1, $this->options[$this->getFieldKey('TEST_SMS')]);
				$tabControl->AddViewField("CSR_CONNECT_OK", "", GetMessage("CSR_CONNECT_OK"));
			} else {
                $tabControl->AddViewField($this->getFieldKey('BALANCE'), GetMessage("CSR_BALANCE"), $balance);
            }
		}
		else{
			$tabControl->AddViewField("CSR_NOTE_SMS_PROVIDER", "", GetMessage("CSR_NEED_REGISTER", array("#SMSLINK#"=>"https://sms.ru")));
		}
	}

    public function getDefaultOptions() {
        return array(
            $this->getFieldKey('BALANCE') => '',
            $this->getFieldKey('TOKEN') => '',
            $this->getFieldKey('SENDERS') => '',
            $this->getFieldKey('TEST_SMS') => 0,
        );
    }
}