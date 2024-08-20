<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Loader;

Loader::includeModule('landing');

$uri = new \Bitrix\Main\Web\Uri(\Bitrix\Landing\Domain::getHostUrl());
$scheme = $uri->getScheme();
$jsObjectName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $this->randString());
?>

<div class="block-cities">
	<div class="block-cities__selected-city"><?= $arResult['CURRENT_CITY']['NAME'] ?></div>
	<div class="block-cities__list-city">
		<? foreach ($arResult["REGIONS"] as $keyCity => $valueCity) { ?>
			<a href="<?= $scheme . '://' . $valueCity['DOMAIN'] ?>"><?= $valueCity["NAME"] ?></a>
		<? } ?>
	</div>
</div>
<?
$jsParams = [
	'SHOW_LIST_BUTTON_CLASS' => 'block-cities__selected-city',
	'LIST_CITY_CLASS' => 'block-cities__list-city'
];
?>
<script>
	var <?= $jsObjectName ?> = new JCMultiregionalitySelectCity(<?= CUtil::PhpToJSObject($jsParams, false, true) ?>);
</script>
