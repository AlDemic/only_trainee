<?
use \Bitrix\Main\Localization\Loc;
/*
    EDIT_CALLBACK и VIEW_CALLBACK => null, так как класс CUserTypeString -> deprecated since main 20.0.700;
    регистрируем публичные классы:
    - getEditFormHTML -> отрисовка в элементе;
    - getSettingsHTML -> отрисовка настроек свойства;
    - prepareSettings -> подготовка параметров перед сохранением.

    Без регистрации(глобальный): OnBeforeSave -> переопределяем сохранение поля под свойство
*/

class CCustomTypeCompAlex
{
    private static $showedCss = false;

	public static function getUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "customcomp",
			"CLASS_NAME"   => "CCustomTypeCompAlex",
			"DESCRIPTION"  => Loc::getMessage('UFCUST_DESC'),
			"BASE_TYPE"    => "string",
            'USER_TYPE' => 'C',
			'EDIT_CALLBACK'        => null,
            'VIEW_CALLBACK'        => null,
            'GetEditFormHTML'      => ['CCustomTypeCompAlex', 'getEditFormHTML'],
            'GetSettingsHTML'  => ['CCustomTypeCompAlex', 'getSettingsHTML'],
            'PrepareSettings'  => ['CCustomTypeCompAlex', 'prepareSettings'],
		);
	}

    public static function getSettingsHTML($arUserField, $arHtmlControl, &$bVarsFromForm)
    {
        $btnAdd = Loc::getMessage('IEX_CPROP_SETTING_BTN_ADD');
        $settingsTitle =  Loc::getMessage('IEX_CPROP_SETTINGS_TITLE');

        $bVarsFromForm = array(
            'SETTINGS_TITLE' => $settingsTitle,
            'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SEARCHABLE', 'SMART_FILTER', 'WITH_DESCRIPTION', 'FILTRABLE', 'MULTIPLE_CNT', 'IS_REQUIRED'),
            'SET' => array(
                'MULTIPLE_CNT' => 1,
                'SMART_FILTER' => 'N',
                'FILTRABLE' => 'N',
            ),
        );

        self::showJsForSetting($arHtmlControl["NAME"]);
        self::showCssForSetting();

        $result = '<tr><td colspan="2" align="center">
            <table id="many-fields-table" class="many-fields-table internal">        
                <tr valign="top" class="heading mf-setting-title">
                   <td>XML_ID</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_TITLE').'</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_SORT').'</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_TYPE').'</td>
                </tr>';


        $arSetting = self::prepareSettingLocal($arUserField['SETTINGS']);

        if(!empty($arSetting)){
            foreach ($arSetting as $code => $arItem) {
                $result .= '
                       <tr valign="top">
                           <td><input type="text" class="inp-code" size="20" value="'.$code.'"></td>
                           <td><input type="text" class="inp-title" size="35" name="'.$arHtmlControl["NAME"].'['.$code.'_TITLE]" value="'.$arItem['TITLE'].'"></td>
                           <td><input type="text" class="inp-sort" size="5" name="'.$arHtmlControl["NAME"].'['.$code.'_SORT]" value="'.$arItem['SORT'].'"></td>
                           <td>
                                <select class="inp-type" name="'.$arHtmlControl["NAME"].'['.$code.'_TYPE]">
                                    '.self::getOptionList($arItem['TYPE']).'
                                </select>                        
                           </td>
                       </tr>';
            }
        }

        $result .= '
               <tr valign="top">
                    <td><input type="text" class="inp-code" size="20"></td>
                    <td><input type="text" class="inp-title" size="35"></td>
                    <td><input type="text" class="inp-sort" size="5" value="500"></td>
                    <td>
                        <select class="inp-type"> '.self::getOptionList().'</select>                        
                    </td>
               </tr>
             </table>   
                
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <input type="button" value="'.$btnAdd.'" onclick="addNewRows()">
                    </td>
                </tr>
                </td></tr>';

        return $result;
    }
	
    /*
        В отличии от CIBlockProp значение хранится в $arUserField['VALUE'],
        поэтому при вызове функций рендера полей передаём его, вместо $value
    */
	public static function getEditFormHTML($arUserField, $arHtmlControl)
    {
        $hideText = Loc::getMessage('IEX_CPROP_HIDE_TEXT');
        $clearText = Loc::getMessage('IEX_CPROP_CLEAR_TEXT');

        self::showCss();

        if(!empty($arUserField['SETTINGS'])){
            $arFields = self::prepareSettingLocal($arUserField['SETTINGS']);
        }
        else{
            return '<span>'.Loc::getMessage('IEX_CPROP_ERROR_INCORRECT_SETTINGS').'</span>';
        }

        $result = '';
        $result .= '<div class="mf-gray"><a class="cl mf-toggle">'.$hideText.'</a>';
        if($arUserField['MULTIPLE'] === 'Y'){
            $result .= ' | <a class="cl mf-delete">'.$clearText.'</a></div>';
        }
        $result .= '<table class="mf-fields-list active">';


        foreach ($arFields as $code => $arItem){
            if($arItem['TYPE'] === 'string'){
                $result .= self::showString($code, $arItem['TITLE'], $arUserField['VALUE'], $arHtmlControl);
            }
            else if($arItem['TYPE'] === 'html'){
                $result .= self::showHTML($code, $arItem['TITLE'], $arUserField['VALUE'], $arHtmlControl);
            }
        }

        $result .= '</table>';

        return $result;
    }

    //
    public static function onBeforeSave($arUserField, $value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }

    public static function prepareSettings($arUserField)
    {
        $result = [];
        if (!empty($arUserField['SETTINGS'])) {
            foreach ($arUserField['SETTINGS'] as $code => $value) {
                $result[$code] = $value;
            }
        }
        return $result;
    }

    //Для корректного формирования name используем ключ 'NAME' в $arHtmlControl вместо 'VALUE'
    private static function showString($code, $title, $arValue, $arHtmlControl)
    {
        $result = '';

        $v = !empty($arValue) ? json_decode($arValue)->$code : '';
        $result .= '<tr>
                    <td align="right">'.$title.': </td>
                    <td><input type="text" value="'.$v.'" name="'.$arHtmlControl['NAME'].'['.$code.']"/></td>
                </tr>';

        return $result;
    }

    private static function showHTML($code, $title, $arValue, $arHtmlControl)
    {
        $result = '';

        $v = !empty($arValue) ? json_decode($arValue)->$code : '';

        $name = $arHtmlControl['NAME'] . '[' . $code . ']';

        $nameEditor = preg_replace("/[^a-zA-Z0-9_:\.]/is", "", $name);

        $html = '<input type="hidden" name="'.$name.'" id="hid_'.$nameEditor.'" value="'.$v.'">';

        ob_start();
		
		CFileMan::AddHTMLEditorFrame(
			$nameEditor,
			$v,
			$nameEditor . "_TYPE",
			strlen($v) ? "html" : "text",
			[
				'height' => 80,
            ]
		);
		
		$html .= ob_get_contents();
		ob_end_clean();

        $html .= '
            <script>
                document.querySelector("form").addEventListener("submit", function() {
                    var nameEditor = "'.$nameEditor.'";
                    var textarea = document.querySelector("textarea[name=\'" + nameEditor + "\']");

                    if(textarea) {
                        document.getElementById("hid_" + nameEditor).value = textarea.value;
                    }
                });
            </script>
        ';

        $result .= '<tr>
                        <td align="right">'.$title.': </td>
                        <td>'.$html.'</td>
                    </tr>';

        return $result;
    }

    private static function showCss()
    {
        if(!self::$showedCss) {
            self::$showedCss = true;
            ?>
            <style>
                .cl {cursor: pointer;}
                .mf-gray {color: #797777;}
                .mf-fields-list {display: none; padding-top: 10px; margin-bottom: 10px!important; margin-left: -300px!important; border-bottom: 1px #e0e8ea solid!important;}
                .mf-fields-list.active {display: block;}
                .mf-fields-list td {padding-bottom: 5px;}
                .mf-fields-list td:first-child {width: 300px; color: #616060;}
                .mf-fields-list td:last-child {padding-left: 5px;}
                .mf-fields-list input[type="text"] {width: 350px!important;}
                .mf-fields-list textarea {min-width: 350px; max-width: 650px; color: #000;}
                .mf-fields-list img {max-height: 150px; margin: 5px 0;}
                .mf-img-table {background-color: #e0e8e9; color: #616060; width: 100%;}
                .mf-fields-list input[type="text"].adm-input-calendar {width: 170px!important;}
                .mf-file-name {word-break: break-word; padding: 5px 5px 0 0; color: #101010;}
                .mf-fields-list input[type="text"].mf-inp-bind-elem {width: unset!important;}
            </style>
            <?
        }
    }

    private static function showJsForSetting($inputName)
    {
        ?>
        <script>
            (function () {
                function run() {
                    window.addNewRows = function () {
                        const html =
                            '<tr valign="top">' +
                            '<td><input type="text" class="inp-code" size="20"></td>' +
                            '<td><input type="text" class="inp-title" size="35"></td>' +
                            '<td><input type="text" class="inp-sort" size="5" value="500"></td>' +
                            '<td><select class="inp-type"><?=CUtil::JSEscape(self::getOptionList())?></select></td>' +
                            '</tr>';
                        jQuery("#many-fields-table").append(html);
                    };

                    jQuery(document).on('change', '.inp-code', function () {
                        const code = jQuery(this).val();
                        const row = jQuery(this).closest('tr');

                        if (code.length <= 0) {
                            row.find('input.inp-title').removeAttr('name');
                            row.find('input.inp-sort').removeAttr('name');
                            row.find('select.inp-type').removeAttr('name');
                        } else {
                            row.find('input.inp-title').attr('name', '<?=$inputName?>[' + code + '_TITLE]');
                            row.find('input.inp-sort').attr('name', '<?=$inputName?>[' + code + '_SORT]');
                            row.find('select.inp-type').attr('name', '<?=$inputName?>[' + code + '_TYPE]');
                        }
                    });

                    jQuery(document).on('input', '.inp-sort', function () {
                        const num = jQuery(this).val();
                        jQuery(this).val(num.replace(/[^0-9]/g, ''));
                    });
                }

                if (typeof jQuery === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js';
                    script.onload = run;
                    document.head.appendChild(script);
                } else {
                    run();
                }
            })();
        </script>
        <?php
    }

    private static function showCssForSetting()
    {
        if(!self::$showedCss) {
            self::$showedCss = true;
            ?>
            <style>
                .many-fields-table {margin: 0 auto; /*display: inline;*/}
                .mf-setting-title td {text-align: center!important; border-bottom: unset!important;}
                .many-fields-table td {text-align: center;}
                .many-fields-table > input, .many-fields-table > select{width: 90%!important;}
                .inp-sort{text-align: center;}
                .inp-type{min-width: 125px;}
            </style>
            <?
        }
    }

    private static function prepareSettingLocal($arSetting)
    {
        $arResult = [];

        foreach ($arSetting as $key => $value){
            if(strstr($key, '_TITLE') !== false) {
                $code = str_replace('_TITLE', '', $key);
                $arResult[$code]['TITLE'] = $value;
            }
            else if(strstr($key, '_SORT') !== false) {
                $code = str_replace('_SORT', '', $key);
                $arResult[$code]['SORT'] = $value;
            }
            else if(strstr($key, '_TYPE') !== false) {
                $code = str_replace('_TYPE', '', $key);
                $arResult[$code]['TYPE'] = $value;
            }
        }

        if(!function_exists('cmp')){
            function cmp($a, $b)
            {
                if ($a['SORT'] == $b['SORT']) {
                    return 0;
                }
                return ($a['SORT'] < $b['SORT']) ? -1 : 1;
            }
        }

        uasort($arResult, 'cmp');

        return $arResult;
    }

    private static function getOptionList($selected = 'string')
    {
        $result = '';
        $arOption = [
            'string' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_STRING'),
            'html' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_HTML'),
        ];

        foreach ($arOption as $code => $name){
            $s = '';
            if($code === $selected){
                $s = 'selected';
            }

            $result .= '<option value="'.$code.'" '.$s.'>'.$name.'</option>';
        }

        return $result;
    }

}
?>