<?
$MESS["MODULE_EXPIRED_DESCRIPTION_LINK"] = "The demo period of the module is over. To continue using, to buy the module, follow the link: <a href='/bitrix/admin/update_system_market.php?module=#MODULE_ID#&lang=en'> Buy module </a> ";
$MESS["CTWEB_SMSAUTH_SETTINGS_TITLE"] = "Settings";
$MESS["CTWEB_SMSAUTH_REGISTER_TITLE"] = "Registration";
$MESS["CTWEB_SMSAUTH_SETTINGS_TITLE_DESC"] = "Module settings";

$MESS["CSR_MODULE_ACTIVE"] = "Active";
$MESS["CSR_DEBUG"] = "Debug Mode";

$MESS["CTWEB_SMSAUTH_SOURCES"] = "Data Sources";
$MESS["CSR_PHONE_FIELD"] = "Field \"Phone\"";
$MESS['CSR_WARNING_REQUIRED_PHONE_NUMBER'] = '<div class="adm-info-message">Please, disable "Register users by phone number" option in <a href="/bitrix/admin/settings.php?lang=ru&mid=main&mid_menu=1" target="_blank">main module settings</a> for prevent registration errors</div>';
$MESS['CSR_CHECK_PHONE_ERRORS'] = 'Check phone number in database for errors';
$MESS["CTWEB_PHONE_FIELD_PERSONAL_PHONE"] = "(Personal) Phone";
$MESS["CTWEB_PHONE_FIELD_PERSONAL_FAX"] = "(Personal) Fax";
$MESS["CTWEB_PHONE_FIELD_PERSONAL_MOBILE"] = "(Personal) Mobile phone";
$MESS["CTWEB_PHONE_FIELD_PERSONAL_PAGER"] = "(Personal) Pager";

$MESS["CTWEB_PHONE_FIELD_WORK_PHONE"] = "(Work) Phone";
$MESS["CTWEB_PHONE_FIELD_WORK_FAX"] = "(Work) Fax";
$MESS["CTWEB_PHONE_FIELD_WORK_PAGER"] = "(Work) Pager";

$MESS["CSR_CODE_LENGTH"] = "Length of generated code";
$MESS["CSR_CODE_SALT"] = "Salt";
$MESS["CSR_TIME_EXPIRE"] = "Time to expire (in seconds)";
$MESS["CSR_CODE_EXAMPLE"] = "Example of generated code";

$MESS["CWSA_SMS_SETTINGS"] = "Message settings";
$MESS["CSR_MESSAGE"] = "Message text";
$MESS["CWSA_SMS_DEFAULT_MESSAGE_TEXT"] = "Code for authorization: #CODE#";
$MESS["CSR_TRANSLIT"] = "Message with transliteration";
$MESS["CSR_TEST_SMS"] = "Test message";

$MESS["CWSA_VENDOR_SETTINGS"] = "Settings";
$MESS["CWSA_VENDOR_API"] = "API_ID";

$MESS["CTWEB_SMSAUTH_PROVIDER_SETTINGS"] = "SMS provider settings";
$MESS["CSR_PROVIDER"] = "SMS provider";

$MESS['CSR_NEEDED_CURL'] = "<div class=\"adm-info-message\">All SMS gateways require cURL to work!</div>";
$MESS['CSR_NOT_SETTINGS'] = "Please select your SMS provider first";
$MESS['CSR_API_KEY'] = "API key";


$MESS['CSR_MODULE_ACTIVE'] = "Module is active";
$MESS['CSR_PROVIDERS'] = "SMS provider";
$MESS['CSR_SETTINGS_PROVAIDER'] = "SMS provider settings";
$MESS['CSR_SETTINGS_FIELD_PHONE'] = "Phone number field";
$MESS['CSR_EMAIL'] = "Email";
$MESS['CSR_LOGIN'] = "Login";
$MESS['CSR_ID_CLIENT'] = "Client ID";
$MESS['CSR_PASS'] = "Password";
$MESS['CSR_TYPE_SEND'] = "Send type";
$MESS['CSR_ON_MODERATE'] = "Moderated";
$MESS['CSR_ON_REJECTED'] = "Rejected";

$MESS['CSR_WARNING'] = "Warning!";
$MESS['CSR_SENDERS'] = "Sender";
$MESS['CSR_SENDERS_NOT_FOUND'] = "Senders not found";
$MESS['CSR_TRANSLIT'] = "Send in transliteration?";
$MESS['CSR_TEXT_MESSAGE'] = "Message text";
$MESS['CSR_TEXT_MESSAGE_EXAMPLE'] = "Your order #ORDER_ID# from #ORDER_DATE# has been paid." . PHP_EOL . "Your check: #CHECK_LINK#.";
$MESS['CSR_AVIABLE_FIELDS'] = "<div class=\"adm-info-message\">#ORDER_ID# - order code <br />
#ORDER_DATE# - order date <br />
#ORDER_USER# - customer <br />
#ORDER_ACCOUNT_NUMBER_ENCODE# - order code (for links) <br />
#CHECK_LINK# - link to the check </div> ";

$MESS['CSR_NEED_REGISTER'] = '<div class="adm-info-message"><a href="#SMSLINK#" target="_blank" class="required">Provider registration required!</a></div>';

$MESS['CSR_SHORTED_URL'] = 'Shrink Link';
$MESS['CSR_SHORTENER'] = 'Shrink Link Using Google URL Shortener';
$MESS['CSR_NOT_SHROT'] = 'Don\'t shrink';
$MESS['CSR_SHORT_GOOGLE'] = 'Via Google';
$MESS['CSR_GOOGLE_API'] = 'API Key for Google URL Shortener';
$MESS['CSR_GOOGLE_API_NOTE'] = '<div class="adm-info-message"><a href="http://code.google.com/apis/console/" target="_blank" class="required">Get API Key</a></div> ';
$MESS['CSR_NEED_CURL'] = '<div class="adm-info-message">Requires cURL to work!</div>';

$MESS['CSR_BALANCE'] = 'Balance';
$MESS['CSR_CONNECT_OK'] = '<div class="adm-info-message-green"><div class="adm-info-message" style="padding-left: 30px;">Connection was successful</div>div></div> ';
$MESS['CSR_CHECK_API'] = '<div class="adm-info-message">Connection failed. #ERROR_TEXT#</div>';
$MESS['CSR_NEW_PROVIDER_NOTE'] = '<div class="adm-info-message-red"> <div class="adm-info-message" style="padding-left: 30px;"><b>Attention!</b> A new SMS provider has been selected!<br /> Click the Save or Apply button to display the actual fields for the selected SMS provider. </div></div> ';
$MESS['CSR_DEBUG_NOTE'] = '<div class="adm-info-message" style="padding-left: 30px;"> If <b>Debug Mode</b> is enabled, no SMS will be sent. <br />Instead, the <b>generated code</b> will be saved in the <b>Logging</b> tab</div>';

$MESS['CTWEB_YES'] = "Yes";
$MESS['CTWEB_CHECKED'] = "checked";
$MESS['CSR_CLEAR_LOG'] = "Clear log";
$MESS['CSR_ORDER_ID'] = "Order number";
$MESS['CSR_ORDER_DATE'] = "Order Date";
$MESS['CSR_ORDER_PHONE'] = "Phone number";
$MESS['CSR_MESSAGE_NUMBER'] = "Message No.";

$MESS['CSR_SMSAERORU_TYPE_SEND_1'] = "Paid letter signature for all telecom operators. Attention, paid signature can be requested only through your personal account";
$MESS['CSR_SMSAERORU_TYPE_SEND_2'] = "Free letter signature for all operators, except MTS. Attention, this type of sending is set by default";
$MESS['CSR_SMSAERORU_TYPE_SEND_3'] = "Free letter signature for all operators (+0.15 rubles to the Direct Channel tariff)";
$MESS['CSR_SMSAERORU_TYPE_SEND_4'] = "Info signature for all operators";
$MESS['CSR_SMSAERORU_TYPE_SEND_6'] = "International delivery (Operators of the Russian Federation and Kazakhstan)";

$MESS['CSR_TYPE_CHANNEL'] = "Send channel selection";
$MESS['CSR_SMSAERORU_TYPE_CHANNEL_SEND_1'] = "Direct Channel";
$MESS['CSR_SMSAERORU_TYPE_CHANNEL_SEND_2'] = "Digital Channel";

$MESS['CTWEB_LOG_ERR'] = "Logging";
$MESS['CSR_PHONE_LENGTH'] = "Minimum phone length";
$MESS['CSR_CODE_ALPHABET'] = "Used characters in the code";
$MESS['CSR_LOGGING'] = "Log";

$MESS['CSR_COMPONENT_SETTINGS'] = "Registration component settings";
$MESS['CSR_REGISTER_FIELDS'] = "Fields in registration component";

$MESS['CSR_NEW_LOGIN_AS'] = "Login field during registration (if not specified)";
$MESS['CSR_NEW_EMAIL_AS'] = "Email field during registration (if not specified)";

$MESS['CSR_ALLOW_REGISTER_AUTH'] = "Authorize instead registration";

$MESS['CWSA_AUTOMATIC'] = "Autofix";
$MESS['CWSA_MAYBE_INCORRECT_NUMBER'] = "Maybe incorrect phone number";
$MESS['CWSA_APPLY_FIX'] = "Apply autofix";

?>
