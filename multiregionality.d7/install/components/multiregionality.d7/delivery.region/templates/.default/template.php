<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$jsObjectName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $this->randString());
$jsParams = $arResult;
$jsParams['PARAMS'] = $arParams;
$jsParams["CURRENT_REGION"]["LIST_SETTLEMENTS_IN_REGION"] = unserialize($arResult["CURRENT_REGION"]["LIST_SETTLEMENTS_IN_REGION"]);
$jsParams["CURRENT_REGION"]["LIST_SETTLEMENTS_IN_REGION"] = array_shift($jsParams["CURRENT_REGION"]["LIST_SETTLEMENTS_IN_REGION"]);
$jsParams["TEMPLATE_PATH"] = $this->GetFolder();

$coordinates = explode(',', $arResult["CURRENT_REGION"]["COORDINATES"]);
$jsParams["CURRENT_REGION"]["COORDINATES"] = [trim($coordinates[1]), trim($coordinates[0])];

foreach ($jsParams["CURRENT_REGION"]["LIST_SETTLEMENTS_IN_REGION"] as $keySettlement => $valueSettlement) {
	$coordinates = explode(',', $valueSettlement['COORDINATES_SETTLEMENT']);
	$jsParams["CURRENT_REGION"]["LIST_SETTLEMENTS_IN_REGION"][$keySettlement]['COORDINATES_SETTLEMENT'] = [trim($coordinates[1]), trim($coordinates[0])];
}
?>

<div id="<?= $arParams['ID_MAP'] ?>" style="width: <?= $arParams['CARD_WIDTH'] ?>; height: <?= $arParams['CARD_HEIGHT'] ?>"></div>
<script>
	var <?= $jsObjectName ?> = new JCMultiregionalityDeliveryRegion(<?= CUtil::PhpToJSObject($jsParams, false, true) ?>);
</script>
