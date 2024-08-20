<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$jsObjectName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $this->randString());
$formattedPhone = '';
?>

<div class="contact-details">
	<? //foreach ($arResult["CURRENT_CITY"]["TELEPHONE"] as $keyContact => $valueContact) {
		//$formattedPhone = \Multiregionality\d7\RegionInfo::getCurrentCity($valueContact); ?>
		<!--<a href="tel:<?//= $formattedPhone ?>" class="contact-details__telephone"><?//= $valueContact ?></a>-->
	<? //} ?>

	<? $formattedPhone = \Multiregionality\d7\Main::formatPhoneNumber($arResult["CURRENT_CITY"]["TELEPHONE"]); ?>
	<a href="tel:<?= $formattedPhone ?>" class="contact-details__telephone"><?= $arResult["CURRENT_CITY"]["TELEPHONE"] ?></a>
</div>
<?
//$jsParams = [
//	'SHOW_LIST_BUTTON_CLASS' => 'block-cities__selected-city',
//	'LIST_CITY_CLASS' => 'block-cities__list-city'
//];
?>
<script>
	var <?= $jsObjectName ?> = new JCMultiregionalityContactDetails(<?= CUtil::PhpToJSObject($jsParams, false, true) ?>);
</script>
