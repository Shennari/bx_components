<div class="bx-system-auth-form">
    <?php
    if ($arResult['AUTH_RESULT'] == 'SUCCESS' || $arResult['STEP'] == 4) {
        include __DIR__. '/success.php';
    } elseif ($arResult['STEP'] == 1) {
        include __DIR__. '/phone.php';
    } elseif ($arResult['STEP'] == 3) {
        include __DIR__. '/code.php';
    } else {
        throw new Exception('error');
    }
    ?>


</div>
