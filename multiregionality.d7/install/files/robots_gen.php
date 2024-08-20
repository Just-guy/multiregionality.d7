<? require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

rename('robots.txt.hide', 'robots.txt');

$pageContent = '<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main\Loader;

Loader::includeModule("landing");
$host = \Bitrix\Main\Context::getCurrent()->getRequest()->getHttpHost();
$uri = new \Bitrix\Main\Web\Uri(\Bitrix\Landing\Domain::getHostUrl());
$scheme = $uri->getScheme();
$subdomain = explode(".", $host);

$host = $_SERVER["HTTP_HOST"];
$host = preg_replace("/\:\d+/is", "", $host);
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
    $protocol = "https";
}
else{
    $protocol = "http";
}
header("Content-Type: text/plain");?>';

$pageContent .= PHP_EOL . file_get_contents('robots.txt');

$pageContent = preg_replace('/Host: ?https:\/\/gidrolux.ru(\/?)/m', 'Host: <?=$protocol?>://<?=$host;?>', $pageContent);
$pageContent = preg_replace('/Sitemap: ?https:\/\/gidrolux.ru\//m', 'Sitemap: <?=$protocol?>://<?=$host;?>/', $pageContent);

$pageContent .= PHP_EOL . '<? if (count($subdomain) > 2) {
	echo PHP_EOL . "User-Agent: Googlebot" . PHP_EOL;
	echo "Disallow: /";
}';

rename('robots.txt', 'robots.txt.hide');

file_put_contents('robots.php', $pageContent);
