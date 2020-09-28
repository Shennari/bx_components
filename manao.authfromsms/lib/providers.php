<?
//Â äàííîì ôàéëå îïðåäåëÿåì ñïèñîê ïðîâàéäåðîâ, ñ êîòîðûìè ðàáîòàåì
//Ïîäêëþ÷àåì èñïîëíÿåìûå ôàéëû, ïî êàæäîìó ïðîâàéäåðó îòäåëüíî

class CTWEB_SMSAUTH_PROVIDERS {

    function __construct(){
        //Ïîäêëþ÷àåì ôàéëû ñ îáðàáîò÷èêàìè
        self::includeFiles();
    }

    function getProviders(){
        //Â äàííîì ôàéëå îïðåäåëÿåì ñïèñîê ïðîâàäåðîâ, ñ êîòîðûìè áóäåì ðàáîòàòü
        $arProviders = array(
            "smsru" => "sms.ru",
            "smscru" => "smsc.ru",
            "smsaeroru" => "smsaero.ru",
            "redsmsru" => "redsms.ru",
            "bytehandcom" => "bytehand.com",
            "iqsmsru" => "iqsms.ru",
            "infosmskaru" => "infosmska.ru",
            "p1smsru" => "p1sms.ru",
            "itsmsru" => "it-sms.ru",
            "prostorsmsru" => "prostor-sms.ru",
            "smssendingru" => "sms-sending.ru",
            "smsuslugiru" => "sms-uslugi.ru",
        );

        ksort($arProviders);

        //Ñîðòèðóåì ìàññèâ ïî àëôàâèòó
        asort($arProviders);

        return $arProviders;
    }

    function getProvcurl(){
        //Â äàííîì ôàéëå îïðåäåëÿåì ñïèñîê ïðîâàäåðîâ, ñ êîòîðûìè áóäåì ðàáîòàòü
        $arProviders = array(
            "smsru",
            "smscru",
            "smsaeroru",
            "bytehandcom",
            "iqsmsru",
            "p1smsru",
            "itsmsru",
            "prostorsmsru",
            "smssendingru",
            "infosmskaru",
            "smsuslugiru",
        );

        return $arProviders;
    }

    function includeFiles(){
        $arProviders = self::getProviders();

        foreach($arProviders as $file=>$val){
            require_once('providers/cwsa_'.$file.'.php');
        }
    }

}