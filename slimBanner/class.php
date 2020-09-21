<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;

class CSlimBanner extends CBitrixComponent implements Controllerable
{

    function executeComponent()
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        if (!$this->arParams['IBLOCK_ID'] || !is_numeric($this->arParams['IBLOCK_ID'])) {
            return;
        }

        if ($this->startResultCache()) {
            $this->arResult = $this->getBannerMassage();
            $this->arResult['COOKIE'] = $this->isCookieExists();
            $this->includeComponentTemplate();
        }
    }

    function getBannerMassage()
    {
        $result = array();
        $filter = array(
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y'
        );
        $dbResult = \Bitrix\Iblock\ElementTable::query()
            ->setSelect(array('ID', 'NAME'))
            ->setFilter($filter)
            ->setCacheTtl(3600)
            ->exec();

        if ($row = $dbResult->fetch()) {
            $result['TEXT'] = $row['NAME'];
        }

        return $result;
    }

    function isCookieExists()
    {
        if(is_null($this->getCookie())) {
            return false;
        } else {
            return true;
        }
    }

    function getCookie()
    {
        $request = Bitrix\Main\Context::getCurrent()->getRequest();
        $cookie = $request->getCookie('SLIM_BANNER');
        return $cookie;
    }

    function setCookieAction()
    {
        $request = Bitrix\Main\Context::getCurrent()->getResponse();
        $cookie = new \Bitrix\Main\Web\Cookie('SLIM_BANNER','1', time() + 86400);
        $cookie->setSecure(false);
        $request->addCookie($cookie);
        $request->flush('');
    }

    function configureActions() {}

}