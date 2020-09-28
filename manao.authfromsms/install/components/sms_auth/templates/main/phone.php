<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=POST_FORM_ACTION_URI?>" class="iarga_form_t valid" id="auth_form">
    <?=bitrix_sessid_post()?>
    <p class="title">Войти в личный кабинет</p>
    <p class="auth-desc">по номеру телефона</p>

    <div class="input-box">
        <div class="auth-input-wrapper boxValid">
            <input type="tel" id="user-phone-login" class="input-text phone-masked req required" placeholder="Ваш телефон" placeholder=" " name="PHONE" data-msg-required="Заполните поле" required="" aria-invalid="false">
        </div>
    </div>

    <div class="check">
        <div>
            <input class="required req" type="checkbox" id="USER_CHECK" name="check" required data-msg="Обязательное поле">
            <label class="check-label" for="USER_CHECK">
                Нажимая кнопку “Отправить код” я соглашаюсь на обработку моих персональных данных в соответствии с
                <a href="/politika-konfidentsialnosti/" target="_blank">Условиями</a>
            </label>
        </div>
    </div>

    <div class="input-box login-buttons">
        <input type="submit" value="Отправить код" class="bt bt50">
        <div class="boxLink">
            <a class="link" href="/support/" target="_blank">Проблемы со входом?</a>
        </div>
    </div>
</form>