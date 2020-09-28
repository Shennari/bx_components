<?
//���� ����������
use Bitrix\Main\Config\Option;

//���� � ����������
require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/manao.authfromsms/include.php');

//���������� ���� � ������������ ����������
require_once('vendor/smsru/autoload.php');

class cwsa_smsru{
	
	public function __construct($module_id){
	}

	/************************************************
		�����������
	************************************************/
	public function Auth(){
		
	}

	/************************************************
		�������� ������
	************************************************/	
	public function getBalance($module_id){
		//�������� ����
		$apiId = Option::get($module_id, 'CSR_SMSRU_TOKEN');
		
		//������������
		$client = new \Zelenin\SmsRu\Api(new \Zelenin\SmsRu\Auth\ApiIdAuth($apiId));
		
		$balance = $client->myBalance();

		if($balance->code == 100){
			return $balance->balance;
		}
		else{
			$msg = self::GetAttr($balance, 'availableDescriptions');
			return $msg[$balance->code];
		}
	}
	
	/************************************************
		�������� ����
	************************************************/
	public function getCodes($id = '0'){
		$msgs = array(
			"-1"=>"��������� �� �������.",
			"100"=>"��������� ��������� � ����� �������",
			"101"=>"��������� ���������� ���������",
			"102"=>"��������� ���������� (� ����)",
			"103"=>"��������� ����������",
			"104"=>"�� ����� ���� ����������: ����� ����� �������",
			"105"=>"�� ����� ���� ����������: ������� ����������",
			"106"=>"�� ����� ���� ����������: ���� � ��������",
			"107"=>"�� ����� ���� ����������: ����������� �������",
			"108"=>"�� ����� ���� ����������: ���������",
			"130"=>"�� ����� ���� ����������: ��������� ���������� ��������� �� ���� ����� � ����",
			"131"=>"�� ����� ���� ����������: ��������� ���������� ���������� ��������� �� ���� ����� � ������",
			"132"=>"�� ����� ���� ����������: ��������� ���������� ���������� ��������� �� ���� ����� � ����",
			"200"=>"������������ api_id",
		    "201"=> "�� ������� ������� �� ������� �����.",
		    "202"=> "����������� ������ ����������.",
		    "203"=> "��� ������ ���������.",
		    "204"=> "��� ����������� �� ����������� � ��������������.",
		    "205"=> "��������� ������� ������� (��������� 8 ���).",
		    "206"=> "����� �������� ��� ��� �������� ������� ����� �� �������� ���������.",
		    "207"=> "�� ���� ����� (��� ���� �� �������) ������ ���������� ���������, ���� ������� ����� 100 ������� � ������ �����������.",
		    "208"=> "�������� time ������ �����������.",
		    "209"=> "�� �������� ���� ����� (��� ���� �� �������) � ����-����.",
			"210"=>"������������ GET, ��� ���������� ������������ POST",
			"211"=>"����� �� ������",
			"220"=>"������ �������� ����������, ���������� ���� �����.",
			"230"=>"�������� ����� ����� ���������� ��������� �� ���� ����� � ����.",
			"231"=>"�������� ����� ���������� ��������� �� ���� ����� � ������.",
			"232"=>"�������� ����� ���������� ��������� �� ���� ����� � ����.",
			"300"=>"������������ token (�������� ����� ���� ��������, ���� ��� IP ���������)",
			"301"=>"������������ ������, ���� ������������ �� ������",
			"302"=>"������������ �����������, �� ������� �� ����������� (������������ �� ���� ���, ���������� � ��������������� ���)"
		);
		
		if(!isset($msgs[$id]))
			return '�� ��������� ����� �����: '.$id;
		
		return $msgs[$id];
	}
	
	/************************************************
		���������� ���������
	************************************************/
	public function sendSMS($module_id, $arFields){

		//�������� ����
		$apiId = Option::get($module_id, 'CSR_SMSRU_TOKEN');
		$apiId = $apiId;
		
		//������������
		$client = new \Zelenin\SmsRu\Api(new \Zelenin\SmsRu\Auth\ApiIdAuth($apiId));
		
		//�������� ����� ���������
		$text = Option::get($module_id, 'CSR_TEXT_MESSAGE');
		
		//� ������������ ����������
		foreach($arFields as $key=>$field){
			$text = str_replace('#'.$key.'#', $field, $text);
		}

		//������� ���������
		$sms = new \Zelenin\SmsRu\Entity\Sms($arFields["PHONE"], $text);
		
		//��������
		if(Option::get($module_id, 'CSR_TRANSLIT') == 1){
			$sms->translit = 1;
		}
		else{
			$sms->translit = 0;
		}

        if(Option::get($module_id, 'CSR_SMSRU_TEST_SMS') == 1) {
            $sms->test = 1;
        }
        else {
            $sms->test = 0;
        }
		
		//����������� ���������
		$sms->partner_id=182951;
		
		//�����������
		$sedner = Option::get($module_id, 'CSR_SMSRU_SENDERS');
		if(!empty($sedner)){
			$sms->from=$sedner;
		}
		
		//���������� SMS
		$res = $client->smsSend($sms);

		$status_code = $res->code;
		$arFields["RESULT_MSG"] = $status_code.': '.self::getCodes($status_code);
		
		switch ($status_code) {
		    case 100:
		    case 101:
		    case 102:
		    case 103:
		    	//��������� ���������� �������
		    	$arFields["RESULT"] = 'success';
		        break;
		    default:
		    	//������ ��� �������� ���������
		    	$arFields["RESULT"] = 'fail';
		    	self::toLog($status_code.': '.self::getCodes($status_code));
		}
		
		self::toLog($arFields);
	}
	
	/************************************************
		�������� ������������
	************************************************/
	public function getSenders($tabControl, $module_id){		
		$token = self::getToken($module_id);
		
		$body=file_get_contents("https://sms.ru/my/senders?api_id=".$token);
		
		$reply = array_filter(explode("\n", $body));
		$code = array_shift($reply);
		
		$arSenders = array();
		foreach($reply as $s){
			$arSenders[$s] = $s;
		}
		if ($code=="100") {
			$tabControl->AddDropDownField("CSR_SMSRU_SENDERS", GetMessage("CSR_SENDERS"), false, $arSenders, Option::get($module_id, 'CSR_SMSRU_SENDERS'));
		}
		else{
			$tabControl->AddViewField("CSR_SMSRU_SENDERS", "", GetMessage("CSR_SENDERS_NOT_FOUND"));
		}
	}
	
	/************************************************
		�������� ����� ��� �������
	************************************************/
	public function getForm($tabControl, $module_id){
		$token = Option::get($module_id, 'CSR_SMSRU_TOKEN');
		$tabControl->AddEditField("CSR_SMSRU_TOKEN", GetMessage('CSR_API_KEY'), false, array("size"=>30, "maxlength"=>255), $token);
		if(!empty($token)){
			$balance = self::getBalance($module_id);
			if(is_float($balance)){
				$tabControl->AddViewField("CSR_SMSRU_BALANCE", GetMessage("CSR_BALANCE"), $balance);
                $tabControl->AddCheckBoxField("CSR_SMSRU_TEST_SMS", GetMessage("CSR_TEST_SMS"), false, 1, Option::get($module_id, "CSR_SMSRU_TEST_SMS", false) == 1);
				$tabControl->AddViewField("CSR_CONNECT_OK", "", GetMessage("CSR_CONNECT_OK"));
			}
			else{
				$tabControl->AddViewField("CSR_NOTE_SMS_PROVIDER", "", GetMessage("CSR_CHECK_API", Array("#ERROR_TEXT#" => $balance)));
			}
		}
		else{
			$tabControl->AddViewField("CSR_NOTE_SMS_PROVIDER", "", GetMessage("CSR_NEED_REGISTER", array("#SMSLINK#"=>"https://ctweb.sms.ru")));	
		}
	}
	
	/************************************************
		�������� ����������� �����
	************************************************/
	public function getToken($module_id){
		return Option::get($module_id, 'CSR_SMSRU_TOKEN');
	}
	
	/************************************************
		���������� � ���
	************************************************/
	public function toLog($arFields){
		$handler = new CityWebSmsAuth_Handler();
		$handler->addToLog($arFields);
	}
	
	/************************************************
		�������� protected
	************************************************/
	public function GetAttr( $obj , $attrName ) {
	    $a = (array)$obj;
	    if ( isset($a[ $attrName ] ) ) {
	        return $a[ $attrName ];
	    }
	    foreach($a as $k => $v) {
	        if ( preg_match("#".preg_quote("\x00" . $attrName)."$#" , $k) ) {
	            return $v;
	        }
	    }
	    return null;
	}
}