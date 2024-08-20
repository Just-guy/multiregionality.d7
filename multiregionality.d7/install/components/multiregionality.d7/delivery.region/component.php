<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\SystemException;

$arResult['CURRENT_REGION'] = \Multiregionality\d7\Main::getCurrentCity();

try {
	if (!$arResult['CURRENT_REGION']) throw new SystemException('Ошибка! Отсутствуют настройки текущего города');
	$this->IncludeComponentTemplate();
} catch (SystemException $exception) {
	echo $exception->getMessage();
}
