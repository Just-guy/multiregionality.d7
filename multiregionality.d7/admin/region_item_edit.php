<?

use Bitrix\Main\Context;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

global $APPLICATION;
CJSCore::Init(array('masked_input'));
IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("multiregionality.d7");

if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$aTabs = [
	[
		"DIV"  => "edit1",
		"TAB"   => "Общие",
		"ICON"  => "main_user_edit",
		"TITLE" => "Форма добавление региона"
	]
];
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$sTableID       = "b_region_list";
$ID             = intval($ID);
$message        = null;
$bVarsFromForm  = false;

if ($REQUEST_METHOD == "POST" && ($save != "" || $apply != "") && $POST_RIGHT == "W" && check_bitrix_sessid()) {
	$arUrl = parse_url($URL);

	$URL = $arUrl["path"] . (isset($arUrl["query"]) ? "?" . $arUrl["query"] : "");

	$rsData = $DB->Query(
		"SELECT * FROM b_region_list 
		WHERE NAME = " . "'" . $DB->ForSql(trim($NAME)) . "'" . ";"
	);

	if (($arItem = $rsData->Fetch()) && $arItem["ID"] != $ID) {
		$res = false;
		$message = new CAdminMessage("Ошибка сохранения элемента, такой регион уже существует");
		$bVarsFromForm = true;
	} else {
		$DB->PrepareFields($sTableID);
		$arFields = array(
			//"MAIN_REGION"						=> "'" . $DB->ForSql(trim($MAIN_REGION)) . "'",
			"ACTIVE"								=> "'" . $DB->ForSql(trim($ACTIVE)) . "'",
			"DOMAIN"								=> "'" . $DB->ForSql(trim($DOMAIN)) . "'",
			"NAME"								=> "'" . $DB->ForSql(trim($NAME)) . "'",
			"NAME_GENITIVE_CASE"				=> "'" . $DB->ForSql(trim($NAME_GENITIVE_CASE)) . "'",
			"NAME_INSTRUMENTAL_CASE"		=> "'" . $DB->ForSql(trim($NAME_INSTRUMENTAL_CASE)) . "'",
			"NAME_PREPOSITIONAL_CASE"		=> "'" . $DB->ForSql(trim($NAME_PREPOSITIONAL_CASE)) . "'",
			"COORDINATES"						=> "'" . $DB->ForSql(trim($COORDINATES)) . "'",
			"ZOOM"						=> "'" . $DB->ForSql(trim($ZOOM)) . "'",
			"LIST_SETTLEMENTS_IN_REGION"	=> "'" . $DB->ForSql(trim(serialize($LIST_SETTLEMENTS_IN_REGION))) . "'",
			"ADDRESS"							=> "'" . $DB->ForSql(trim($ADDRESS)) . "'",
			"TELEPHONE"							=> "'" . $DB->ForSql(trim($TELEPHONE)) . "'",
			"EMAIL"								=> "'" . $DB->ForSql(trim($EMAIL)) . "'",
			"TEXT"								=> "'" . $DB->ForSql(trim($TEXT)) . "'"
		);

		$DB->StartTransaction();

		if ($ID > 0) {
			$res = $DB->Update($sTableID, $arFields, "WHERE ID = '{$ID}'", $err_mess . __LINE__);
		} else {
			$ID = $DB->Insert($sTableID, $arFields, $err_mess . __LINE__);
			$res = ($ID > 0);
		}

		if (strlen($strError) <= 0) {
			$DB->Commit();
			$res = true;
		} else {
			$DB->Rollback();
		}


		if ($res) {
			if ($apply != "")
				LocalRedirect("/bitrix/admin/region_item_edit.php?ID=" . $ID . "&mess=ok&lang=" . LANG . "&" . $tabControl->ActiveTabParam());
			else
				LocalRedirect("/bitrix/admin/region_list.php?lang=" . LANG);
		} else {
			if ($e = $APPLICATION->GetException())
				$message = new CAdminMessage("Ошибка сохранения элемента", $e);
			$bVarsFromForm = true;
		}
	}
}

//$str_MAIN_REGION						= "";
$str_ACTIVE								= "";
$str_DOMAIN								= "";
$str_NAME								= "";
$str_NAME_GENITIVE_CASE				= "";
$str_NAME_INSTRUMENTAL_CASE		= "";
$str_NAME_PREPOSITIONAL_CASE		= "";
$str_COORDINATES						= "";
$str_ZOOM								= "";
$str_ADDRESS							= "";
$str_TELEPHONE							= "";
$str_EMAIL								= "";
$str_LIST_SETTLEMENTS_IN_REGION	= "";
$str_TEXT								= "";

if ($ID > 0) {
	$rsData = $DB->Query("SELECT * FROM b_region_list WHERE ID = {$ID};");
	if (!$rsData->ExtractFields("str_"))
		$ID = 0;
}

$str_LIST_SETTLEMENTS_IN_REGION = unserialize(htmlspecialcharsback($str_LIST_SETTLEMENTS_IN_REGION));

if ($bVarsFromForm)
	$DB->InitTableVarsForEdit("b_region_list", "", "str_");

$currentRegion = Multiregionality\d7\Main::getListCities(['ID' => $ID]);

$APPLICATION->SetTitle(($ID > 0 ? "Редактирование записи: " . $ID : "Добавление записи"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT"  => "Список регионов",
		"TITLE" => "Список регионов",
		"LINK"  => "region_list.php?lang=" . LANG,
		"ICON"  => "btn_list",
	)
);

if ($ID > 0) {
	$aMenu[] = array("SEPARATOR" => "Y");
	$aMenu[] = array(
		"TEXT"  => "Добавить элемент",
		"TITLE" => "Добавить элемент",
		"LINK"  => "rubric_edit.php?lang=" . LANG,
		"ICON"  => "btn_new",
	);
	$aMenu[] = array(
		"TEXT"  => "Удалить элемент",
		"TITLE" => "Удалить элемент",
		"LINK"  => "javascript:if(confirm('" . "Вы уверены что хостите удалить элемент?" . "'))window.location='rubric_admin.php?ID=" . $ID . "&action=delete&lang=" . LANG . "&" . bitrix_sessid_get() . "';",
		"ICON"  => "btn_delete",
	);
}

$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($_REQUEST["mess"] == "ok" && $ID > 0)
	CAdminMessage::ShowMessage(array("MESSAGE" => "Данные сохранены", "TYPE" => "OK"));

if ($message)
	echo $message->Show();
elseif ($DB->GetErrorMessage() != "")
	CAdminMessage::ShowMessage($DB->GetErrorMessage());
?>
<form method="POST" Action="<?= $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
	<?= bitrix_sessid_post() ?>
	<? $tabControl->Begin(); ?>
	<? $tabControl->BeginNextTab(); ?>
	<tr>
		<td>Активность</td>
		<td><input type="checkbox" name="ACTIVE" value="Y" <? if (!empty($str_ACTIVE) && ($str_ACTIVE == true || $str_ACTIVE == 'Y')) echo 'checked' ?>>
		</td>
	</tr>
	<tr>
		<td>Домен (без http/https)</td>
		<td><input type="text" name="DOMAIN" value="<?= $str_DOMAIN; ?>" size="50"></td>
	</tr>
	<tr>
		<td>Название региона</td>
		<td><input type="text" name="NAME" value="<?= $str_NAME; ?>" size="50"></td>
	</tr>
	<tr>
		<td>Название города в родительном падеже</td>
		<td><input type="text" name="NAME_GENITIVE_CASE" value="<?= $str_NAME_GENITIVE_CASE; ?>" size="50"></td>
	</tr>
	<tr>
		<td>Название города в творительном падеже</td>
		<td><input type="text" name="NAME_INSTRUMENTAL_CASE" value="<?= $str_NAME_INSTRUMENTAL_CASE; ?>" size="50"></td>
	</tr>
	<tr>
		<td>Название города в предложном падеже</td>
		<td><input type="text" name="NAME_PREPOSITIONAL_CASE" value="<?= $str_NAME_PREPOSITIONAL_CASE; ?>" size="50"></td>
	</tr>
	<tr>
		<td>Координаты для карты</td>
		<td><input type="text" name="COORDINATES" value="<?= $str_COORDINATES; ?>" size="30"></td>
	</tr>
	<tr>
		<td>Масштабирование карты</td>
		<td><input type="text" name="ZOOM" value="<?= $str_ZOOM; ?>" size="30"></td>
	</tr>

	<!--  -->

	<? if (empty($str_LIST_SETTLEMENTS_IN_REGION)) : ?>
		<tr class="multiregionality-filed" data-container="settlements">
			<td class="multiregionality-filed__title">Список населенных пунктов региона</td>
			<td class="multiregionality-filed__value">
				<input type="text" name="LIST_SETTLEMENTS_IN_REGION[<?= $ID ?>][0][SETTLEMENTS]" value="" size="30">
				<input type="text" name="LIST_SETTLEMENTS_IN_REGION[<?= $ID ?>][0][COORDINATES_SETTLEMENT]" value="" size="30">
			</td>
		</tr>
	<? else : ?>
		<? foreach ($str_LIST_SETTLEMENTS_IN_REGION[$ID] as $keySettlements => $valueSettlements) { ?>
			<tr class="multiregionality-filed" data-container="settlements">
				<? if ($keySettlements == 0) : ?>
					<td class="multiregionality-filed__title">Список населенных пунктов региона</td>
				<? else : ?>
					<td class="adm-detail-content-cell-l"></td>
				<? endif; ?>
				<td class="multiregionality-filed__value">
					<input type="text" name="LIST_SETTLEMENTS_IN_REGION[<?= $ID ?>][<?= $keySettlements ?>][SETTLEMENTS]" value="<?= $valueSettlements['SETTLEMENTS'] ?>" size="30">
					<input type="text" name="LIST_SETTLEMENTS_IN_REGION[<?= $ID ?>][<?= $keySettlements ?>][COORDINATES_SETTLEMENT]" value="<?= $valueSettlements['COORDINATES_SETTLEMENT'] ?>" size="30">
					<? if ($keySettlements != 0) : ?>
						<input type="button" class="multiregionality-filed__delete-field adm-btn-delete" value="x">
					<? endif; ?>
				</td>
			</tr>
		<? } ?>
	<? endif; ?>

	<tr>
		<td></td>
		<td>
			<input type="button" class="multiregionality-filed__add adm-btn-save" data-container-button="settlements" value="Добавить населенный пункт">
		</td>
	</tr>

	<tr>
		<td>Адрес</td>
		<td><input type="text" name="ADDRESS" value="<?= $str_ADDRESS; ?>" size="50"></td>
	</tr>
	<tr>
		<td>Телефон</td>
		<td><input type="text" name="TELEPHONE" value="<?= $str_TELEPHONE; ?>" size="50"></td>
	</tr>
	<tr>
		<td>E-mail</td>
		<td><input type="text" name="EMAIL" value="<?= $str_EMAIL; ?>" size="50"></td>
	</tr>
	<tr>
		<td>Описание</td>
		<td>
			<? $APPLICATION->IncludeComponent(
				"bitrix:fileman.light_editor",
				"",
				array(
					"CONTENT" => htmlspecialchars_decode($str_TEXT),
					"INPUT_NAME" => "TEXT",
					"INPUT_ID" => "",
					"WIDTH" => "100%",
					"HEIGHT" => "300px",
					"RESIZABLE" => "Y",
					"AUTO_RESIZE" => "Y",
					"VIDEO_ALLOW_VIDEO" => "Y",
					"VIDEO_MAX_WIDTH" => "640",
					"VIDEO_MAX_HEIGHT" => "480",
					"VIDEO_BUFFER" => "20",
					"VIDEO_LOGO" => "",
					"VIDEO_WMODE" => "transparent",
					"VIDEO_WINDOWLESS" => "Y",
					"VIDEO_SKIN" => "/bitrix/components/bitrix/player/mediaplayer/skins/bitrix.swf",
					"USE_FILE_DIALOGS" => "Y",
					"ID" => "",
					"JS_OBJ_NAME" => ""
				)
			); ?>
		</td>
	</tr>
	<?
	$tabControl->Buttons(
		array(
			"disabled" => ($POST_RIGHT < "W"),
			"back_url" => "region_list.php?lang=" . LANG,
		)
	);
	?>

	<input type="hidden" name="lang" value="<?= LANG ?>">
	<? if ($ID > 0 && !$bCopy) : ?>
		<input type="hidden" name="ID" value="<?= $ID ?>">
	<? endif; ?>
	<? $tabControl->End(); ?>
	<? $tabControl->ShowWarnings("post_form", $message); ?>
</form>
<script>
	let filed = '',
		buttonAdd = '',
		dataContainer = '',
		listFileds = 0,
		lastElementContainer = '',
		templateField = '';

	document.addEventListener('click', (event) => {
		if (event.target.classList.contains('multiregionality-filed__add')) {
			debugger
			dataContainer = event.target.dataset.containerButton;
			listFileds = document.querySelectorAll('[data-container=' + dataContainer + ']');
			lastElementContainer = listFileds[listFileds.length - 1];
			let templateField =
				`<tr class="multiregionality-filed" data-container="` + dataContainer + `">
					<td class="adm-detail-content-cell-l"></td>
					<td class="multiregionality-filed__value adm-detail-content-cell-r">
						<input type="text" name="LIST_SETTLEMENTS_IN_REGION[<?= $ID ?>][` + (listFileds.length + 1) + `][SETTLEMENTS]" value="" size="30">
						<input type="text" name="LIST_SETTLEMENTS_IN_REGION[<?= $ID ?>][` + (listFileds.length + 1) + `][COORDINATES_SETTLEMENT]" value="" size="30">
						<input type="button" class="adm-btn-delete" value="x">
					</td>
				</tr>`;
			lastElementContainer.insertAdjacentHTML('afterend', templateField);
		}

		if (event.target.classList.contains('multiregionality-filed__delete-field')) {
			event.target.closest('.multiregionality-filed').remove();
		}
	})

	debugger
	let inputPhone = document.querySelector("input[name=TELEPHONE]")
	let phoneMasked = new BX.MaskedInput({
		mask: '+7 999 999 9999', // устанавливаем маску
		input: inputPhone,
		placeholder: '_' // символ замены +7 ___ ___ __ __
	});
	if (inputPhone.value != '') {
		inputPhone.value = inputPhone.value.replace(/[^\d]/g, "").trim(); //убираем все нечисла
		phoneMasked.setValue(inputPhone.value.substring(inputPhone.value.length, (inputPhone.value.length - 10))); // устанавливаем значение без первой 7
	}
</script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
