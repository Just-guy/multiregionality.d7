<?php

namespace Multiregionality\d7;

use COption;
use \Bitrix\Main\Context;

class Main
{
	static $module_id = "multiregionality.d7";

	// public static function OnBeforeEndBufferContent()
	// {
	//     GLOBAL $APPLICATION;

	//     if(Context::getCurrent()->getRequest()->isAdminSection()) return;

	//     $url        = defined('SEOEVERYWHERE_REQUEST_URI') ? SEOEVERYWHERE_REQUEST_URI : $_SERVER['REQUEST_URI'];
	//     $domain     = $_SERVER['HTTP_HOST'];
	//     $subdomain  = false;

	//     if(mb_substr_count($domain, '.') == 2) {
	//         list($subdomain, $domain) = explode('.', $domain, 2);
	//     }

	//     $meta = MultiregionalityMeta::getMeta($url, $subdomain);

	//     if($meta) {
	//         MultiregionalityMeta::setMeta($meta);
	//     } else {
	//         $filter = MultiregionalityMeta::getFilter(self::$module_id);

	//         if($filter && (count($filter) > 1 || !isset($filter["FACET_OPTIONS"]))) {
	//             $options = [
	//                 'FILTER_HEAD'           => COption::GetOptionString(self::$module_id, "FILTER_HEAD"),
	//                 'FILTER_TITLE'          => COption::GetOptionString(self::$module_id, "FILTER_TITLE"),
	//                 'FILTER_DESCRIPTION'    => COption::GetOptionString(self::$module_id, "FILTER_DESCRIPTION"),
	//             ];
	//             $category = MultiregionalityMeta::getCategory(self::$module_id);

	//             foreach ($options as $key => $option) {
	//                 $params = MultiregionalityMeta::parseParams($option);
	//                 if($category) {
	//                     $params["CATEGORY"]["VALUE"] = $category;
	//                 }

	//                 MultiregionalityMeta::prepareParams($params, $filter);
	//                 $text = MultiregionalityMeta::insertParams($params, $option);

	//                 switch ($key) {
	//                     case 'FILTER_HEAD':
	//                         $APPLICATION->SetTitle($text);
	//                         break;
	//                     case 'FILTER_TITLE':
	//                         $APPLICATION->SetPageProperty('title', $text);
	//                         break;
	//                     case 'FILTER_DESCRIPTION':
	//                         $APPLICATION->SetPageProperty('description', $text);
	//                         break;
	//                 }
	//             }
	//         }
	//     }
	// }

	public static function OnEndBufferContent(&$content)
	{
		$isAdmin = Context::getCurrent()->getRequest()->isAdminSection();
		if ($isAdmin) return;

		$currentCity = self::getCurrentCity();

		if (preg_match('/{city.name}/', $content)) {
			$content = preg_replace('/{city.name}/', $currentCity["NAME"], $content);
		}

		if (preg_match('/{city.name.genitive}/', $content)) {
			$content = preg_replace('/{city.name.genitive}/', $currentCity["NAME_GENITIVE_CASE"], $content);
		}

		if (preg_match('/{city.name.instrumental}/', $content)) {
			$content = preg_replace('/{city.name.instrumental}/', $currentCity["NAME_INSTRUMENTAL_CASE"], $content);
		}

		if (preg_match('/{city.name.prepositional}/', $content)) {
			$content = preg_replace('/{city.name.prepositional}/', $currentCity["NAME_PREPOSITIONAL_CASE"], $content);
		}

		if (preg_match('/{city.address}/', $content)) {
			$content = preg_replace('/{city.address}/', $currentCity["ADDRESS"], $content);
		}

		if (preg_match('/{city.telephone}/', $content)) {
			$content = preg_replace('/{city.telephone}/', $currentCity["TELEPHONE"], $content);
		}

		if (preg_match('/{city.email}/', $content)) {
			$content = preg_replace('/{city.email}/', $currentCity["EMAIL"], $content);
		}

		if (preg_match('/{city.text}/', $content)) {
			$content = preg_replace('/{city.text}/', $currentCity["TEXT"], $content);
		}
	}

	public static function OnPageStart()
	{
		$isAdmin = Context::getCurrent()->getRequest()->isAdminSection();
		if ($isAdmin) return;
		$optionKeyYandexMapV3 = \Bitrix\Main\Config\Option::get('multiregionality.d7', 'multiregionality_yandex_map_key_v3');
		if (!empty($optionKeyYandexMapV3)) \Bitrix\Main\Page\Asset::getInstance()->addString('<script src="https://api-maps.yandex.ru/v3/?apikey=' . $optionKeyYandexMapV3 . '&lang=ru_RU"></script>');
		//global $APPLICATION;

		//define('MULTIREGIONALITY', 'YES');

		//  $context        = \Bitrix\Main\Application::getInstance()->getContext();
		//  $request        = $context->getRequest();
		//  $server         = $context->getServer();
		//  $request_uri    = $request->getRequestUri();

		//  $data = \Multiregionality\MultiregionalityLink::getLink($request_uri);
		//  if($data) {
		//      if($data['REDIRECT']) {
		//          $request_uri = $data['NEW'];
		//          if($data['QUERY']) {
		//              $request_uri .= "?" . $data['QUERY'];
		//          }
		//          LocalRedirect($request_uri, false, '301 Moved Permanently');
		//      } else {
		//          $request_uri = $data['OLD'];
		//          if($data['QUERY']) {
		//              $request_uri .= "?" . $data['QUERY'];
		//          }
		//          $request_new_uri = $data['NEW'];
		//          if($data['QUERY']) {
		//              $request_new_uri .= "?" . $data['QUERY'];
		//          }

		//          $serverArray                = $server->toArray();
		//          $_SERVER['REQUEST_URI']     = $request_uri;
		//          $serverArray['REQUEST_URI'] = $request_uri;
		//          $server->set($serverArray);

		//          $context->initialize(new \Bitrix\Main\HttpRequest($server, $_GET, [], [], $_COOKIE), $context->getResponse(), $server);
		//          $APPLICATION->reinitPath();

		//          $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$data[NEW]";
		//          define('SEOEVERYWHERE_LINK_NEW', $actual_link);
		//          define('SEOEVERYWHERE_REQUEST_URI', $request_new_uri);
		//      }
		//  } else {
		//	define('MULTIREGIONALITY', 'YES');
		//  }
	}

	// public static function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	// {
	//     $aModuleMenu[] = Array(
	//         "parent_menu"   => "global_menu_content",
	//         "section"       => "content",
	//         "icon"          => "util_menu_icon",
	//         "page_icon"     => "util_menu_icon",
	//         "sort"          => "0",
	//         "text"          => "СЕО везде",
	//         "title"         => "СЕО везде",
	//         "url"           => "/bitrix/admin/seoeverywhere_meta.php",
	//         "more_url"      => Array("seoeverywhere_meta.php"),
	//         "items_id"      => "menu_content",
	//         "items"         => Array(
	//             Array(
	//                 "text" => "Список мета тегов",
	//                 "url" => "/bitrix/admin/seoeverywhere_meta.php",
	//                 "more_url" => Array("seoeverywhere_meta")
	//             ),
	//             Array(
	//                 "text" => "Список ссылок",
	//                 "url" => "/bitrix/admin/seoeverywhere_link.php",
	//                 "more_url" => Array("seoeverywhere_link")
	//             ),
	//             Array(
	//                 "text" => "Список тегов",
	//                 "url" => "/bitrix/admin/seoeverywhere_tag.php",
	//                 "more_url" => Array("seoeverywhere_tag")
	//             ),
	//             Array(
	//                 "text" => "Настройки",
	//                 "url" => "/bitrix/admin/settings.php?mid=seoeverywhere",
	//                 "more_url" => Array()
	//             )
	//         )
	//     );
	// }

	public static function getListCities($filter = [])
	{
		// запрос к базе
		$result = RegionListTable::getList(
			[
				'select' => ['*'],
				'filter' => $filter
			]
		);
		// преобразование запроса от базы
		while ($row = $result->fetch()) {
			$resultArray[] = $row;
		}

		// возвращаем ответ от баззы
		return $resultArray;
	}

	public static function getCurrentCity()
	{
		$request = Context::getCurrent()->getRequest();
		//$protocol = ($request->isHttps() ? 'https://' : 'http://');
		$host = $request->getHttpHost();
		//$scheme = $uri->getScheme();

		// запрос к базе
		$result = RegionListTable::getList(
			[
				'select' => ['*'],
				'filter' => ['DOMAIN' => $host, 'ACTIVE' => 'Y']
			]
		);
		// преобразование запроса от базы
		$result = $result->fetch();

		// возвращаем ответ от баззы
		return $result;
	}

	public static function formatPhoneNumber($phone_number)
	{
		$formattedPhone = preg_replace('/[^0-9]/', '', $phone_number);

		if (strlen($formattedPhone) == 11 && $formattedPhone[0] == '8') {
			$formattedPhone = '+7' . substr($formattedPhone, 1);
		} elseif (strlen($formattedPhone) == 10) {
			$formattedPhone = '+7' . $formattedPhone;
		}

		return $formattedPhone;
	}
}
