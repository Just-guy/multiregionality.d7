<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$APPLICATION->SetTitle("Мультирегиональность");

IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("multiregionality.d7");
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "b_region_list";

$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter()
{
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $$f;

	return count($lAdmin->arFilterErrors) == 0;
}

$FilterArr = ["find"];

$lAdmin->InitFilter($FilterArr);

$sWhere = "";
if (CheckFilter()) {
	$sWhere .= "WHERE NAME LIKE '%{$find}%'";
}


if ($lAdmin->EditAction() && $POST_RIGHT == "W") {

	foreach ($FIELDS as $ID => $arFields) {
		if (!$lAdmin->IsUpdated($ID)) {
			continue;
		}

		$DB->StartTransaction();
		$ID = IntVal($ID);

		if (($rsData = $DB->Query("SELECT * FROM {$sTableID} WHERE ID = {$ID};")) && ($arData = $rsData->Fetch())) {
			foreach ($arFields as $key => $value) {
				$arData[$key] = "'" . $DB->ForSql(trim($value)) . "'";
			}

			if (!$DB->Update($sTableID, $arData, "WHERE ID = '{$ID}'")) {
				$lAdmin->AddGroupError("Ошибка сохранения элемента" . " " . $cData->LAST_ERROR, $ID);
				$DB->Rollback();
			}
		} else {
			$lAdmin->AddGroupError("Ошибка сохранения элемента", $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

if (($arID = $lAdmin->GroupAction()) && $POST_RIGHT == "W") {
	if ($_REQUEST['action_target'] == 'selected') {
		$arID = [];
		$rsData = $DB->Query("SELECT ID FROM {$sTableID};");
		while ($arRes = $rsData->Fetch()) {
			$arID[] = $arRes['ID'];
		}
	}

	foreach ($arID as $ID) {
		if (strlen($ID) <= 0) {
			continue;
		}

		$ID = IntVal($ID);

		switch ($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if (!$DB->Query("DELETE FROM {$sTableID} WHERE ID = {$ID};")) {
					$DB->Rollback();
					$lAdmin->AddGroupError("Ошибка удаления записи", $ID);
				}
				$DB->Commit();
				break;
		}
	}
}


$rsData = $DB->Query("SELECT * FROM {$sTableID} {$sWhere};");

$rsData = new CAdminResult($rsData, $sTableID);

$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint("Элементов"));

$lAdmin->AddHeaders(
	[
		[
			"id"        => "ID",
			"content"   => "ID",
			"sort"      => "ID",
			"align"     => "right",
			"default"   => true,
		],
		[
			"id"        => "ACTIVE",
			"content"   => "Активность",
			"sort"      => "ACTIVE",
			"default"   => true,
		],
		[
			"id"        => "DOMAIN",
			"content"   => "Домен",
			"sort"      => "DOMAIN",
			"default"   => true,
		],
		[
			"id"        => "NAME",
			"content"   => "Регион",
			"sort"      => "NAME",
			"default"   => true,
		],
		[
			"id"        => "NAME_GENITIVE_CASE",
			"content"   => "Название города в родительном падеже",
			"sort"      => "NAME_GENITIVE_CASE",
			"default"   => true,
		],
		[
			"id"        => "NAME_INSTRUMENTAL_CASE",
			"content"   => "Название города в творительном падеже",
			"sort"      => "NAME_INSTRUMENTAL_CASE",
			"default"   => true,
		],
		[
			"id"        => "NAME_PREPOSITIONAL_CASE",
			"content"   => "Название города в предложном падеже",
			"sort"      => "NAME_PREPOSITIONAL_CASE",
			"default"   => true,
		],
		[
			"id"        => "COORDINATES",
			"content"   => "Координаты",
			"sort"      => "COORDINATES",
			"default"   => true,
		],
		//[
		//	"id"        => "LIST_SETTLEMENTS_IN_REGION",
		//	"content"   => "E-mail",
		//	"sort"      => "LIST_SETTLEMENTS_IN_REGION",
		//	"default"   => true,
		//],
		[
			"id"        => "ADDRESS",
			"content"   => "Адрес",
			"sort"      => "ADDRESS",
			"default"   => true,
		],
		[
			"id"        => "TELEPHONE",
			"content"   => "Телефон",
			"sort"      => "TELEPHONE",
			"default"   => true,
		],
		[
			"id"        => "EMAIL",
			"content"   => "E-mail",
			"sort"      => "EMAIL",
			"default"   => true,
		],
		//[
		//	"id"        => "TEXT",
		//	"content"   => "Описание",
		//	"sort"      => "TEXT",
		//	"default"   => true,
		//],
	]
);

while ($arRes = $rsData->NavNext(true, "multiregionality_")) {
	$row = &$lAdmin->AddRow($multiregionality_ID, $arRes);

	$arActions = [];

	$arActions[] = [
		"ICON"      => "edit",
		"DEFAULT"   => true,
		"TEXT"      => "Редактировать",
		"ACTION"    => $lAdmin->ActionRedirect("region_item_edit.php?ID=" . $multiregionality_ID)
	];

	if ($POST_RIGHT >= "W")
		$arActions[] = [
			"ICON"      => "delete",
			"TEXT"      => "Удалить",
			"ACTION"    => "if(confirm('" . "Вы уверены что хостите удалить элемент?" . "')) " . $lAdmin->ActionDoGroup($multiregionality_ID, "delete")
		];


	$row->AddActions($arActions);
}

$lAdmin->AddGroupActionTable(["delete" => GetMessage("MAIN_ADMIN_LIST_DELETE")]);

$aContext = [
	[
		"TEXT"  => "Добавить",
		"LINK"  => "region_item_edit.php?lang=" . LANG,
		"TITLE" => "Добавить тег",
		"ICON"  => "btn_new",
	]
];

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
