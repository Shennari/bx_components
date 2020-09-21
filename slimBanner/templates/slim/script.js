$(function () {
    var slimBanner = document.querySelector('.slim-banner');
    var closeButton = slimBanner.querySelector('.icon_type_cross');

    var onCloseButton = function () {
        slimBanner.style = 'display: none;';
        var query = {
            c: 'iarga:slimBanner',
            action: 'setCookie',
            mode: 'class'
        };

        var data = {
            sessid: BX.message('bitrix_sessid')
        };

        var request = $.ajax({
            url: '/bitrix/services/main/ajax.php?' + $.param(query, true),
            method: 'POST',
            data: data
        });
    };

    closeButton.addEventListener('click', onCloseButton);
});