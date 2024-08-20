<?php
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;

// пространство имен для управления (регистрации/удалении) модуля в системе/базе
use Bitrix\Main\ModuleManager;

// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;

// пространство имен с абстрактным классом для любых приложений, любой конкретный класс приложения является наследником этого абстрактного класса
use Bitrix\Main\Application;

// пространство имен для работы c ORM
use \Bitrix\Main\Entity\Base;

// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;

// пространство имен для событий
use \Bitrix\Main\EventManager;


use Bitrix\Main\Diag\Debug;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

class Multiregionality_d7 extends CModule
{

	// переменные модуля
	public  $MODULE_ID;
	public  $MODULE_VERSION;
	public  $MODULE_VERSION_DATE;
	public  $MODULE_NAME;
	public  $MODULE_DESCRIPTION;
	public  $PARTNER_NAME;
	public  $PARTNER_URI;
	public  $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
	public  $MODULE_GROUP_RIGHTS;
	public  $errors;

	public function __construct()
	{
		$arModuleVersion = [];
		include(__DIR__ . '/version.php');
		$this->MODULE_ID           = 'multiregionality.d7';
		$this->MODULE_VERSION      = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME         = Loc::getMessage('MULTIREGIONALITY_D7_NAME');
		$this->MODULE_DESCRIPTION  = Loc::getMessage('MULTIREGIONALITY_D7_DESC');
	}

	public function DoInstall()
	{
		global $APPLICATION;

		ModuleManager::registerModule($this->MODULE_ID);
		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvents();

		$rules = [
			[
				'CONDITION' => '#^/sitemap.xml#',
				'RULE' => '',
				'ID' => NULL,
				'PATH' => '/sitemap.php',
				'SORT' => 100,
			],
			[
				'CONDITION' => '#^/robots.txt#',
				'RULE' => '',
				'ID' => NULL,
				'PATH' => '/robots.php',
				'SORT' => 100,
			]
		];
		foreach ($rules as $rule) {
			\Bitrix\Main\UrlRewriter::add('s1', $rule);
		}

		$APPLICATION->includeAdminFile(
			Loc::getMessage('MULTIREGIONALITY_D7_INSTALL_TITLE') . ' «' . Loc::getMessage('MULTIREGIONALITY_D7_NAME') . '»',
			__DIR__ . '/step.php'
		);
	}

	public function InstallFiles()
	{
		CopyDirFiles(
			__DIR__ . "/components",
			Application::getDocumentRoot() . "/bitrix/components",
			true,
			true
		);
		CopyDirFiles(
			__DIR__ . "/admin",
			Application::getDocumentRoot() . "/bitrix/admin",
			true,
			true
		);

		CopyDirFiles(
			__DIR__ . '/files',
			$_SERVER["DOCUMENT_ROOT"] . '/',
			true, // перезаписывает файлы
			true  // копирует рекурсивно
		);

		return true;
	}

	public function InstallDB()
	{
		Loader::includeModule($this->MODULE_ID);

		\Multiregionality\d7\RegionListTable::exitsOrCreateTable();
	}

	public function InstallEvents()
	{
		//EventManager::getInstance()->registerEventHandler(
		//	'main',
		//	'OnBuildGlobalMenu',
		//	$this->MODULE_ID,
		//	'Multiregionality\\d7\\ultiRegionalityMain',
		//	'OnBuildGlobalMenu'
		//);
		EventManager::getInstance()->registerEventHandler(
			'main',
			'OnPageStart',
			$this->MODULE_ID,
			'Multiregionality\\d7\\Main',
			'OnPageStart'
		);
		EventManager::getInstance()->registerEventHandler(
			'main',
			'OnEndBufferContent',
			$this->MODULE_ID,
			'Multiregionality\\d7\\Main',
			'OnEndBufferContent'
		);
		//EventManager::getInstance()->registerEventHandler(
		//	'main',
		//	'OnBeforeEndBufferContent',
		//	$this->MODULE_ID,
		//	'Multiregionality\\d7\\Main',
		//	'OnBeforeEndBufferContent'
		//);
	}

	public function DoUninstall()
	{
		global $APPLICATION;

		$this->UnInstallFiles();
		$this->UnInstallDB();
		$this->UnInstallEvents();
		ModuleManager::unRegisterModule($this->MODULE_ID);

		$rules = [
			[
				'CONDITION' => '#^/sitemap.xml#',
			],
			[
				'CONDITION' => '#^/robots.txt#',
			]
		];
		foreach ($rules as $rule) {
			\Bitrix\Main\UrlRewriter::delete('s1', $rule);
		}

		$APPLICATION->includeAdminFile(
			Loc::getMessage('MULTIREGIONALITY_D7_UNINSTALL_TITLE') . ' «' . Loc::getMessage('MULTIREGIONALITY_D7_NAME') . '»',
			__DIR__ . '/unstep.php'
		);
	}

	public function UnInstallFiles()
	{
		@unlink(Application::getDocumentRoot() . '/bitrix/admin/region_list.php');
		@unlink(Application::getDocumentRoot() . '/bitrix/admin/region_item_edit.php');

		DeleteDirFilesEx("/bitrix/components/" . $this->MODULE_ID);

		Option::delete($this->MODULE_ID);
	}

	public function UnInstallDB()
	{
		Loader::includeModule($this->MODULE_ID);
		Multiregionality\d7\RegionListTable::dropTable();

		Option::delete($this->MODULE_ID);
	}

	public function UnInstallEvents()
	{
		//EventManager::getInstance()->unRegisterEventHandler(
		//	'main',
		//	'OnBuildGlobalMenu',
		//	$this->MODULE_ID,
		//	'Multiregionality\\Main',
		//	'OnBuildGlobalMenu'
		//);
		EventManager::getInstance()->unRegisterEventHandler(
			'main',
			'OnPageStart',
			$this->MODULE_ID,
			'Multiregionality\\d7\\Main',
			'OnPageStart'
		);
		EventManager::getInstance()->unRegisterEventHandler(
			'main',
			'OnEndBufferContent',
			$this->MODULE_ID,
			'Multiregionality\\d7\\Main',
			'OnEndBufferContent'
		);
		//EventManager::getInstance()->unRegisterEventHandler(
		//	'main',
		//	'OnBeforeEndBufferContent',
		//	$this->MODULE_ID,
		//	'Multiregionality\\Main',
		//	'OnBeforeEndBufferContent'
		//);
	}
}
