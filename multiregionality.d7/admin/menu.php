<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;									// пространство имен для подключений ланговых файлов

Loc::loadMessages(__FILE__);											// подключение ланговых файлов

$aMenu = array(															// сформируем верхний пункт меню
	'parent_menu' => 'global_menu_content',						// пункт меню в разделе Контент
	'sort' => 1,															// сортировка
	'text' => "Мультирегиональность",								// название пункта меню
	"items_id" => "menu_webforms",									// идентификатор ветви
	"icon" => "translate_menu_icon",										// иконка
);			

$aMenu["items"][] =  array(											// дочерния ветка меню
	'text' => 'Список регионов',										// название подпункта меню
	'url' => 'region_list.php?lang=' . LANGUAGE_ID				// ссылка для перехода
);

$aMenu["items"][] =  array(											// дочерния ветка меню
	'text' => 'Админка модуля',										// название подпункта меню
	'url' => 'settings.php?lang=ru&mid=multiregionality.d7'		// ссылка для перехода
);

return $aMenu;																// возвращаем основной массив $aMenu
