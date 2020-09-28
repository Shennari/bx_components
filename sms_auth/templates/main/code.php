<form  method="post" target="_top" action="<?=POST_FORM_ACTION_URI?>" class="iarga_form_t valid" id="auth_form-confirm">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="antibot" value="1">
    <p class="title">Введите код из sms</p>

    <div class="auth-desc">
        мы отправили код на
        <span class="data-number"><?=$arResult['USER_VALUES']["PHONE"]?></span>
        <a class="change disabled" data-modal-open="">изменить</a>
    </div>
    <?if(!empty($arResult['ERRORS'])):?>
    <div class="errors">
        <?foreach($arResult['ERRORS'] as $error){
            echo $error.'<br>';
        }?>
    </div>
    <?endif;?>
    <div class="input-box">
        <div class="auth-input-wrapper boxValid">
            <input type="text" class="input-text req" placeholder="Ваш код" name="CODE" data-msg-required="Заполните поле" required="">
        </div>
    </div>

    <div class="request-code">
        <p class="request-code__label">Запросить код повторно можно через
            <span class="request-code__time"><?=$arResult['EXPIRE_TIME'] - time()?></span>
            <span>секунд</span>
        </p>
        <a class="request-code__link">запросить код</a>
    </div>

    <div class="input-box login-buttons">
        <input type="submit" name="SEND_SMS" value="Отправить" class="bt bt50">
        <div class="boxLink">
            <a class="link" href="/support/" target="_blank">Проблемы со входом?</a>
        </div>
    </div>

</form>

