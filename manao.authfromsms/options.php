<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Manao\AuthSms\Manager;
use Manao\AuthSms\Module;
use Manao\AuthSms\CAdminForm;

Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');

$MODULE_ID = 'manao.authfromsms';
ClearVars();

if(!$USER->IsAdmin())
    $APPLICATION->AuthForm();

$module_state = Loader::includeModule($MODULE_ID);

#
#   Data preparing
#
$manager = new Manager;
$arOptions = Module::getOptions();
$arPhoneFields = Module::getPhoneFieldList();


/*
 *  SAVE SETTINGS
 */
if ($REQUEST_METHOD == 'POST' && ($_POST['save'] || $_POST['apply']) && check_bitrix_sessid()) {

    if ($_POST['CLEAR_LOG']) {
        Module::clearLog();
    }

    Module::updateOptions($_POST);

    LocalRedirect($APPLICATION->GetCurPageParam());
    exit;
}

#
#   TAB CONTROL
#
$aTabs = array();
$aTabs[] = array("DIV"=>"sp_settings_tab", "TAB"=> GetMessage("CTWEB_SMSAUTH_SETTINGS_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("CTWEB_SMSAUTH_SETTINGS_TITLE"));
$aTabs[] = array("DIV" => "csr_log_err",  "TAB" => GetMessage("CTWEB_LOG_ERR"), "TITLE"=>GetMessage("CTWEB_LOG_ERR"));


$tabControl = new CAdminForm("user_edit", $aTabs);

$tabControl->Begin(array(
    "FORM_ACTION" => $APPLICATION->GetCurPage()."?lang=".LANG."&mid=".$MODULE_ID."&mid_menu=1",
    "FORM_ATTRIBUTES" => "",
));

$tabControl->BeginEpilogContent();
echo bitrix_sessid_post();
$tabControl->EndEpilogContent();

#
#   TAB 1
$tabControl->BeginNextFormTab();
$tabControl->AddSection("CSGV_MODULE_SETTINGS", Loc::getMessage("CTWEB_SMSAUTH_SETTINGS_TITLE_DESC"));

$tabControl->AddCheckBoxField("ACTIVE", Loc::getMessage("CSR_MODULE_ACTIVE"), false, 1, $arOptions['ACTIVE']);
$tabControl->AddCheckBoxField("DEBUG", Loc::getMessage("CSR_DEBUG"), false, 1, $arOptions['DEBUG']);
$tabControl->AddViewField("CSR_DEBUG_NOTE", "", GetMessage("CSR_DEBUG_NOTE"));

$tabControl->AddSection("CSGV_SOURCES", Loc::getMessage("CTWEB_SMSAUTH_SOURCES"));
$tabControl->AddDropDownField("PHONE_FIELD", Loc::getMessage("CSR_PHONE_FIELD"), true, $arPhoneFields, $arOptions['PHONE_FIELD']);
if (Module::CoreHasOwnPhoneAuth() && $arOptions['PHONE_FIELD'] !== "PHONE_NUMBER" && Option::get('main', 'new_user_phone_auth', 'N') === 'Y') {
    $tabControl->AddViewField("CSR_WARNING_REQUIRED_PHONE_NUMBER", "", GetMessage("CSR_WARNING_REQUIRED_PHONE_NUMBER"));
} elseif ($arOptions['PHONE_FIELD'] !== "PHONE_NUMBER" && !$arOptions['NO_PHONE_ERRORS']) {
    $tabControl->AddViewField("CSR_CHECK_PHONE_ERRORS", "", "<a href='javascript:void(0);' onclick='checkPhoneErrors()'>".GetMessage('CSR_CHECK_PHONE_ERRORS')."</a>");
}

$tabControl->AddEditField("MIN_PHONE_LENGTH", Loc::getMessage("CSR_PHONE_LENGTH"), true, array(), $arOptions['MIN_PHONE_LENGTH']);
$tabControl->AddEditField("CODE_LENGTH", Loc::getMessage("CSR_CODE_LENGTH"), false, array(), $arOptions['CODE_LENGTH']);
$tabControl->AddEditField("ALPHABET", Loc::getMessage("CSR_CODE_ALPHABET"), false, array(), $arOptions['ALPHABET']);
$tabControl->AddEditField("TIME_EXPIRE", Loc::getMessage("CSR_TIME_EXPIRE"), false, array(), $arOptions['TIME_EXPIRE']);

$code_example = $manager->GenerateCode();
$tabControl->AddViewField("CSR_CODE_EXAMPLE", GetMessage("CSR_CODE_EXAMPLE"), $code_example);


$tabControl->AddSection("CSR_SETTINGS_PROVIDER", GetMessage("CTWEB_SMSAUTH_PROVIDER_SETTINGS"));

$arProviders = Module::getProviderList();
$arProviderArray = array_map(function($e) { return $e['NAME']; }, $arProviders);

$tabControl->AddDropDownField("PROVIDER", Loc::getMessage("CSR_PROVIDER"), true, $arProviderArray, $arOptions['PROVIDER'], array('id="PROVIDER"', 'onchange="showNoteAfterSelect()"'));

if(!function_exists('curl_init')){
    $tabControl->AddViewField("CSR_NEEDED_CURL", "", GetMessage("CSR_NEEDED_CURL"));
}

try {
    $obProvider = Module::getProvider($arOptions['PROVIDER']);
    if ($obProvider) {
        $obProvider->showAuthForm($tabControl);
        $tabControl->AddViewField("CSR_NEW_PROVIDER_NOTE", "", Loc::getMessage("CSR_NEW_PROVIDER_NOTE"));
    }
} catch (\Exception $e) {
    $tabControl->AddViewField("CSR_ERROR", Loc::getMessage("CSR_ERROR"), $e->getMessage());
}

$tabControl->AddSection("CWSA_SMS_SETTINGS", Loc::getMessage("CWSA_SMS_SETTINGS"));
try {
    if ($obProvider) {
        $obProvider->showSendersForm($tabControl);
    }
} catch (\Exception $e) {
    $tabControl->AddViewField("CSR_ERROR", Loc::getMessage("CSR_ERROR"), $e->getMessage());
}
$tabControl->AddTextField("TEXT_MESSAGE", Loc::getMessage("CSR_TEXT_MESSAGE"), $arOptions['TEXT_MESSAGE'], array("cols"=>80, "rows"=>10));
$tabControl->AddCheckBoxField("TRANSLIT", Loc::getMessage("CSR_TRANSLIT"), false, 1, $arOptions['TRANSLIT']);


#
#  Tab 3
#
$tabControl->BeginNextFormTab();
$tabControl->AddCheckBoxField("CLEAR_LOG", Loc::getMessage("CSR_CLEAR_LOG"), false, 1, false);
$tabControl->AddDropDownField("LOG_MESSAGES", Loc::getMessage("CSR_LOGGING"), true, Module::getLogOptions(), $arOptions['LOG_MESSAGES']);

foreach (Module::getLogs() as $message) {
    $tabControl->AddViewField("CSR_LOG_MSGS_VIEW".$message['TIMESTAMP'], $message['TIMESTAMP'], $message['TEXT']);
}



$tabControl->Buttons(array(
    "disabled" => false,
    "btnSave" => true,
    "btnCancel" => false,
    "btnSaveAndAdd" => false,
));

$tabControl->Show();

?><script type="text/javascript">

    function showNoteAfterSelect(){
        var form_wrap = document.getElementById("user_edit_form");
        var new_input = document.createElement("input");
        new_input.setAttribute("type", "hidden");
        new_input.setAttribute("name", "save");
        new_input.setAttribute("value", "Y");

        form_wrap.appendChild(new_input);
        form_wrap.submit();
    }

    function checkPhoneErrors() {
        var linkNode = document.getElementById("tr_CSR_CHECK_PHONE_ERRORS").querySelector('.adm-detail-content-cell-r');

        BX.ajax.runAction('ctweb:smsauth.api.settings.check')
            .then(function (response) {
                var data = response.data;

                linkNode.innerHTML = '';

                var info = document.createElement('p');
                info.innerText = data.message;
                linkNode.appendChild(info);

                var ul = document.createElement('ul');
                linkNode.appendChild(ul);

                var res = data.result;

                for (var i in res) {
                    if (res.hasOwnProperty(i)) {

                        var li = document.createElement('li');
                        li.innerHTML = ['<a target="_blank" href="/bitrix/admin/user_edit.php?lang=ru&ID=', res[i]['user_id'], '">[', res[i]['user_id'], '] ', res[i]['phone'] ,'</a>',
                            ' =&gt; ',
                            res[i]['normalized'],
                            ' - ',
                            res[i]['action'] === 'auto' ? '<?= GetMessage("CWSA_AUTOMATIC") ?>' : '<?= GetMessage("CWSA_MAYBE_INCORRECT_NUMBER") ?>'
                        ].join('');

                        ul.appendChild(li);
                    }
                }

                if (res.filter(function (e) { return e.action === 'auto' }).length > 0) {
                    var applyButton = document.createElement('a');
                    applyButton.innerText = "<?= GetMessage("CWSA_APPLY_FIX") ?>";
                    applyButton.setAttribute('href', 'javascript:void(0)');
                    applyButton.onclick = fixPhoneErrors;
                    linkNode.appendChild(applyButton);
                }

            })
    }

    function fixPhoneErrors() {
        BX.ajax.runAction('ctweb:smsauth.api.settings.fix')
            .then(function (response) {
                if (response.data === true) {
                    location.reload();
                }
            });
    }
</script>
<style>
    #tr_CSR_NEW_PROVIDER_NOTE{display: none;}
</style>