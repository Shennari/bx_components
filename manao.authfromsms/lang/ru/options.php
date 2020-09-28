<?
$MESS["MODULE_EXPIRED_DESCRIPTION_LINK"] = "Демо-период модуля закончился. Для продолжения использования, чтобы купить модуль перейдите по ссылке: <a href='/bitrix/admin/update_system_market.php?module=#MODULE_ID#&lang=ru'>Купить модуль</a>";
$MESS["CTWEB_SMSAUTH_SETTINGS_TITLE"] = "Настройка";
$MESS["CTWEB_SMSAUTH_REGISTER_TITLE"] = "Регистрация";
$MESS["CTWEB_SMSAUTH_SETTINGS_TITLE_DESC"] = "Настройки модуля";

$MESS["CSR_MODULE_ACTIVE"] = "Активный";
$MESS["CSR_DEBUG"] = "Режим отладки";

$MESS["CTWEB_SMSAUTH_SOURCES"] = "Источники данных";
$MESS["CSR_PHONE_FIELD"] = "Поле \"Телефон\"";
$MESS['CSR_WARNING_REQUIRED_PHONE_NUMBER'] = '<div class="adm-info-message">Пожалуйста, отключите опцию "Регистрировать пользователей по номеру телефона" в <a href="/bitrix/admin/settings.php?lang=ru&mid=main&mid_menu=1" target="_blank">настройках главного модуля</a>, чтобы избежать ошибок при регистрации</div>';
$MESS['CSR_CHECK_PHONE_ERRORS'] = 'Проверить телефоны на ошибочную запись';
$MESS["CTWEB_PHONE_FIELD_PERSONAL_PHONE"] = "(Личные данные) Телефон";
$MESS["CTWEB_PHONE_FIELD_PERSONAL_FAX"] = "(Личные данные) Факс";
$MESS["CTWEB_PHONE_FIELD_PERSONAL_MOBILE"] = "(Личные данные) Мобильный телефон";
$MESS["CTWEB_PHONE_FIELD_PERSONAL_PAGER"] = "(Личные данные) Пейджер";

$MESS["CTWEB_PHONE_FIELD_WORK_PHONE"] = "(Работа) Телефон";
$MESS["CTWEB_PHONE_FIELD_WORK_FAX"] = "(Работа) Факс";
$MESS["CTWEB_PHONE_FIELD_WORK_PAGER"] = "(Работа) Пейджер";

$MESS["CSR_CODE_LENGTH"] = "Длина генерируемого кода";
$MESS["CSR_CODE_SALT"] = "Соль";
$MESS["CSR_TIME_EXPIRE"] = "Время до истечения (секунд)";
$MESS["CSR_CODE_EXAMPLE"] = "Пример генерируемого кода";

$MESS["CWSA_SMS_SETTINGS"] = "Настройки сообщения";
$MESS["CSR_MESSAGE"] = "Текст сообщения";
$MESS["CWSA_SMS_DEFAULT_MESSAGE_TEXT"] = "Код для авторизации: #CODE#";
$MESS["CSR_TRANSLIT"] = "Сообщение транслитом";
$MESS["CSR_TEST_SMS"] = "Тестовое сообщение";

$MESS["CWSA_VENDOR_SETTINGS"] = "Настройки";
$MESS["CWSA_VENDOR_API"] = "API_ID";

$MESS["CTWEB_SMSAUTH_PROVIDER_SETTINGS"] = "Настройки SMS-провайдера";
$MESS["CSR_PROVIDER"] = "SMS-Провайдер";

$MESS['CSR_NEEDED_CURL'] = '<div class="adm-info-message">Для работы всех SMS-шлюзов требуется cURL!</div>';
$MESS['CSR_NOT_SETTINGS'] = "Сначала выберите SMS-провайдера";
$MESS['CSR_API_KEY'] = "API ключ";


$MESS['CSR_MODULE_ACTIVE'] = "Модуль активен";
$MESS['CSR_PROVIDERS'] = "SMS-провайдер";
$MESS['CSR_SETTINGS_PROVAIDER'] = "Настройки SMS-провайдера";
$MESS['CSR_SETTINGS_FIELD_PHONE'] = "Поле с номером телефона";
$MESS['CSR_EMAIL'] = "Email";
$MESS['CSR_LOGIN'] = "Логин";
$MESS['CSR_ID_CLIENT'] = "ID клиента";
$MESS['CSR_PASS'] = "Пароль";
$MESS['CSR_TYPE_SEND'] = "Тип отправки";
$MESS['CSR_ON_MODERATE'] = "На модерации";
$MESS['CSR_ON_REJECTED'] = "Отклонено";

$MESS['CSR_WARNING'] = "Внимание!";
$MESS['CSR_SENDERS'] = "Отправитель";
$MESS['CSR_SENDERS_NOT_FOUND'] = "Отправители не найдены";
$MESS['CSR_TRANSLIT'] = "Отправлять в транслите?";
$MESS['CSR_TEXT_MESSAGE'] = "Текст сообщения";
$MESS['CSR_TEXT_MESSAGE_EXAMPLE'] = "Ваш заказ #ORDER_ID# от #ORDER_DATE# оплачен." . PHP_EOL . "Ваш чек: #CHECK_LINK#.";
$MESS['CSR_AVIABLE_FIELDS'] = "<div class=\"adm-info-message\">#ORDER_ID# - код заказа<br />
								#ORDER_DATE# - дата заказа<br />
								#ORDER_USER# - заказчик<br />
								#ORDER_ACCOUNT_NUMBER_ENCODE# - код заказа(для ссылок)<br />
								#CHECK_LINK# - ссылка на чек</div>";


$MESS['CSR_NEED_REGISTER'] = '<div class="adm-info-message"><a href="#SMSLINK#" target="_blank" class="required">Требуется регистрация у провайдера!</a></div>';

$MESS['CSR_SHORTED_URL'] = 'Сжатие ссылки';
$MESS['CSR_SHORTENER'] = 'Сжимать ссылку через Google URL Shortener';
$MESS['CSR_NOT_SHROT'] = 'Не сжимать';
$MESS['CSR_SHORT_GOOGLE'] = 'Через Google';
$MESS['CSR_GOOGLE_API'] = 'API-ключ для Google URL Shortener';
$MESS['CSR_GOOGLE_API_NOTE'] = '<div class="adm-info-message"><a href="http://code.google.com/apis/console/" target="_blank" class="required">Получить API-ключ</a></div>';
$MESS['CSR_NEED_CURL'] = '<div class="adm-info-message">Для работы требуется cURL!</div>';

$MESS['CSR_BALANCE'] = 'Баланс';
$MESS['CSR_CONNECT_OK'] = '<div class="adm-info-message-green"><div class="adm-info-message" style="padding-left: 30px;">Подключение прошло успешно</div></div>';
$MESS['CSR_CHECK_API'] = '<div class="adm-info-message">Подключиться не удалось. #ERROR_TEXT#</div>';
$MESS['CSR_NEW_PROVIDER_NOTE'] = '<div class="adm-info-message-red"><div class="adm-info-message" style="padding-left: 30px;"><b>Внимание!</b> Выбран новый SMS-провайдер!<br />Нажмите кнопку "Сохранить" или  "Применить", чтобы появились актуальные поля для выбранного SMS-провайдера.</div></div>';
$MESS['CSR_DEBUG_NOTE'] = '<div class="adm-info-message" style="padding-left: 30px;">Если включен <b>Режим отладки</b>, СМС отправляться не будет.<br />Вместо этого, <b>сгенерированный код</b> сохранится во вкладке <b>Логирование</b></div>';

$MESS['CTWEB_YES'] = "Да";
$MESS['CTWEB_CHECKED'] = "checked";

$MESS['CSR_CLEAR_LOG'] = "Очистить лог";
$MESS['CSR_ORDER_ID'] = "Номер заказа";
$MESS['CSR_ORDER_DATE'] = "Дата заказа";
$MESS['CSR_ORDER_PHONE'] = "Номер телефона";
$MESS['CSR_MESSAGE_NUMBER'] = "Сообщение №";

$MESS['CSR_SMSAERORU_TYPE_SEND_1'] = "Оплаченная буквенная подпись для всех операторов связи. Внимание, оплаченную подпись можно запросить только через личный кабинет";
$MESS['CSR_SMSAERORU_TYPE_SEND_2'] = "Бесплатная буквенная подпись для всех операторов, кроме МТС. Внимание, данный тип отправки установлен по умолчанию";
$MESS['CSR_SMSAERORU_TYPE_SEND_3'] = "Бесплатная буквенная подпись для всех операторов (+0,15 рублей к тарифу Прямого канала)";
$MESS['CSR_SMSAERORU_TYPE_SEND_4'] = "Инфоподпись для всех операторов";
$MESS['CSR_SMSAERORU_TYPE_SEND_6'] = "Международная доставка (Операторы РФ и Казахстана)";

$MESS['CSR_TYPE_CHANNEL'] = "Выбор канала отправки";
$MESS['CSR_SMSAERORU_TYPE_CHANNEL_SEND_1'] = "Прямой канал";
$MESS['CSR_SMSAERORU_TYPE_CHANNEL_SEND_2'] = "Цифровой канал";

$MESS['CTWEB_LOG_ERR'] = "Логирование";
$MESS['CSR_PHONE_LENGTH'] = "Минимальная длина телефона";
$MESS['CSR_CODE_ALPHABET'] = "Используемые символы в коде";
$MESS['CSR_LOGGING'] = "Логировать";

$MESS['CSR_COMPONENT_SETTINGS'] = "Настройки компонента регистрации";
$MESS['CSR_REGISTER_FIELDS'] = "Поля в компоненте регистрации";

$MESS['CSR_NEW_LOGIN_AS'] = "Поле Логин при регистрации (если не указан)";
$MESS['CSR_NEW_EMAIL_AS'] = "Поле Email при регистрации (если не указан)";

$MESS['CSR_ALLOW_REGISTER_AUTH'] = "Авторизовывать вместо регистрации";

$MESS['CWSA_AUTOMATIC'] = "Автоматически";
$MESS['CWSA_MAYBE_INCORRECT_NUMBER'] = "Возможно, некорректный номер";
$MESS['CWSA_APPLY_FIX'] = "Применить автоматические изменения";

?>
