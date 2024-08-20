<?
// пространство имен модуля
namespace Multiregionality\d7;

// пространство имен для ORM
use \Bitrix\Main\Entity;
// пространство имен для кеша
use \Bitrix\Main\Application;

// сущность ORM унаследованная от DataManager
class RegionListTable extends Entity\DataManager
{
	// название таблицы в базе данных, если не указывать данную функцию, то таблица в бд сформируется автоматически из неймспейса
	public static function getTableName()
	{
		return "b_region_list";
	}

	// подключение к БД, если не указывать, то будет использовано значение по умолчанию подключения из файла .settings.php. Если указать, то можно выбрать подключение, которое может быть описано в .setting.php

	// метод возвращающий структуру ORM-сущности
	public static function getMap()
	{
		/*
         * Типы полей: 
         * DatetimeField - дата и время
         * DateField - дата
         * BooleanField - логическое поле да/нет
         * IntegerField - числовой формат
         * FloatField - числовой дробный формат
         * EnumField - список, можно передавать только заданные значения
         * TextField - text
         * StringField - varchar
         */

		return [
			new Entity\IntegerField(
				"ID",
				[
					"primary" => true,
					"autocomplete" => true,
				]
			),
			new Entity\BooleanField(
				'ACTIVE',
				[
					"values" => ['N', 'Y']
				]
			),
			new Entity\StringField(
				"DOMAIN",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"NAME",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"NAME_GENITIVE_CASE",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"NAME_INSTRUMENTAL_CASE",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"NAME_PREPOSITIONAL_CASE",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"COORDINATES",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"ZOOM",
				[
					'size' => 255
				]
			),
			new Entity\TextField(
				"LIST_SETTLEMENTS_IN_REGION",
				[]
			),
			new Entity\StringField(
				"ADDRESS",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"TELEPHONE",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"EMAIL",
				[
					'size' => 255
				]
			),
			new Entity\StringField(
				"TEXT",
				[
					'size' => 255
				]
			),
		];
	}

	public static function dropTable()
	{
		$connection = \Bitrix\Main\Application::getConnection();
		$connection->dropTable(self::getTableName());
		return true;
	}

	public static function exitsOrCreateTable()
	{
		if (!self::getEntity()->getConnection()->isTableExists(self::getTableName())) {
			self::getEntity()->createDbTable();
		}
		return true;
	}

	// // события можно задавать прямо в ORM-сущности, для примера запретим изменять поле LINK_PICTURE
	// public static function onBeforeUpdate(Entity\Event $event)
	// {
	// 	$result = new Entity\EventResult;
	// 	$data = $event->getParameter("fields");
	// 	if (isset($data["LINK_PICTURE"])) {
	// 		$result->addError(
	// 			new Entity\FieldError(
	// 				$event->getEntity()->getField("LINK_PICTURE"),
	// 				"Запрещено менять LINK_PICTURE код у баннера"
	// 			)
	// 		);
	// 	}
	// 	return $result;
	// }

	// очистка тегированного кеша при добавлении
	//public static function onAfterAdd(Entity\Event $event)
	//{
	//	RegionListTable::clearCache();
	//}
	//// очистка тегированного кеша при изменении
	//public static function onAfterUpdate(Entity\Event $event)
	//{
	//	RegionListTable::clearCache();
	//}
	//// очистка тегированного кеша при удалении
	//public static function onAfterDelete(Entity\Event $event)
	//{
	//	RegionListTable::clearCache();
	//}
	//// основной метод очистки кеша по тегу
	//public static function clearCache()
	//{
	//	// служба пометки кеша тегами
	//	$taggedCache = Application::getInstance()->getTaggedCache();
	//	$taggedCache->clearByTag('popup');
	//}
}
