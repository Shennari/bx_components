<?
namespace Manao\AuthSms;

use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class ProviderBase {
    protected $options;
    protected $code = '';
    protected $api = null;
    protected $login;
    protected $password;
    public $LAST_ERROR = '';

    public function __construct()
    {
        $this->options = Module::getOptions();
    }

    public function getFieldKey($key) {
        return "PROVIDER_{$this->code}_{$key}";
    }

    public function Auth(){
        $this->login = $this->options[$this->getFieldKey('LOGIN')];
        $this->password = $this->options[$this->getFieldKey('PASSWORD')];
        
        return null;
    }

    public function getBalance(){
        return '';
    }

    public function getToken(){
        return $this->options[$this->getFieldKey('TOKEN')];
    }

    protected function GetAttr( $obj , $attrName ) {
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

    public static function getErrorCodes(){
        return array(
            Loc::getMessage('CW_UNKNOWN_ERROR')
        );
    }

    public function getError($id) {
        if ($msg = static::getErrorCodes()[$id]) {
            return $msg;
        } else {
            return reset(self::getErrorCodes());
        }
    }

    protected function PrepareMessage($sMessage, $arParams = array()) {
        foreach($arParams as $key=>$field){
            $sMessage = str_replace('#'.$key.'#', $field, $sMessage);
        }
        $sMessage = preg_replace('/#[^#]+#/', '', $sMessage);
        return $sMessage;
    }

    protected function transliterate($sMessage) {
        $cyr = explode('', Loc::getMessage('CW_CHARS_LIST'));
        $lat = array(
            'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
            'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya',''
        );

        return str_replace($cyr, $lat, $sMessage);
    }
    
    abstract public function sendSMS($arFields);

    public function getSenders() {
        return array();
    }
    public function showSendersForm($tabControl) {
        $arSenders = $this->getSenders();

        if (sizeof($arSenders)>0) {
            $tabControl->AddDropDownField($this->getFieldKey('SENDERS'), GetMessage("CSR_SENDERS"), false, $arSenders, $this->options[$this->getFieldKey('SENDERS')]);
        }
        else{
            $tabControl->AddViewField($this->getFieldKey('SENDERS'), "", GetMessage("CSR_SENDERS_NOT_FOUND"));
        }
    }
    abstract public function showAuthForm($tabControl);

    public function getDefaultOptions() {
        return array(
            $this->getFieldKey('BALANCE') => '',
            $this->getFieldKey('LOGIN') => '',
            $this->getFieldKey('TOKEN') => '',
            $this->getFieldKey('SENDERS') => ''
        );
    }

    public function updateOptions($data) {
        foreach ($this->getDefaultOptions() as $key => $value) {
            $value = $data[$key] ?: $value;
            Option::set(Module::MODULE_ID, $key, $value);
        }
    }
}