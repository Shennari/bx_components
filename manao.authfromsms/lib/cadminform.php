<?php

namespace Manao\AuthSms;

class CAdminForm extends \CAdminForm {
    function AddSelectField($id, $content, $required, $arSelect, $value = array(), $arParams = array())
    {
        if (!is_array($value))
            $value = array();
        $html = '<select name="' . $id . '[]"';
        foreach ($arParams as $param)
            $html .= ' ' . $param;
        $html .= '>';

        foreach ($arSelect as $key => $val)
            $html .= '<option value="' . htmlspecialcharsbx($key) . '"' . (in_array($key, $value) ? ' selected' : '') . '>' . htmlspecialcharsex($val) . '</option>';
        $html .= '</select>';

        $this->tabs[$this->tabIndex]["FIELDS"][$id] = array(
            "id" => $id,
            "required" => $required,
            "content" => $content,
            "html" => '<td width="40%">' . ($required ? '<span class="adm-required-field">' . $this->GetCustomLabelHTML($id, $content) . '</span>' : $this->GetCustomLabelHTML($id, $content)) . '</td><td>' . $html . '</td>',
            "hidden" => '<input type="hidden" name="' . $id . '" value="' . '' . '">',
        );
    }
}