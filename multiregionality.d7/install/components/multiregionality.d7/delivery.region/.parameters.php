<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
	"GROUPS" => [],
	"PARAMETERS" => [
		"CARD_WIDTH" => [
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("DR_CARD_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "100%"
		],
		"CARD_HEIGHT" => [
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("DR_CARD_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "400px"
		],
		"ID_MAP" => [
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("DR_ID_MAP"),
			"TYPE" => "STRING",
			"DEFAULT" => "map-delivery-region"
		],
	],
];
