<? require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
$host = $_SERVER["HTTP_HOST"];
$subdomain = explode('.', $host);
$host = preg_replace("/\:\d+/is", "", $host);
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
	$http = "https";
} else {
	$http = "http";
}
header("Content-Type: text/plain"); ?>
User-Agent: *
Disallow: */index.php
Disallow: /bitrix/
Disallow: /*show_include_exec_time=
Disallow: /*show_page_exec_time=
Disallow: /*show_sql_stat=
Disallow: /*bitrix_include_areas=
Disallow: /*clear_cache=
Disallow: /*clear_cache_session=
Disallow: /*ADD_TO_COMPARE_LIST
Disallow: /*ORDER_BY
Disallow: /*PAGEN
Disallow: /*?print=
Disallow: /*?list_style=
Disallow: /*?sort=
Disallow: /*?set_filter=
Disallow: /*?arrFilter*
Disallow: /*?order=
Disallow: /*&print=
Disallow: /*print_course=
Disallow: /*?action=
Disallow: /*&action=
Disallow: /*register=
Disallow: /*forgot_password=
Disallow: /*change_password=
Disallow: /*login=
Disallow: /*logout=
Disallow: /*auth=
Disallow: /*backurl=
Disallow: /*back_url=
Disallow: /*BACKURL=
Disallow: /*BACK_URL=
Disallow: /*back_url_admin=
Disallow: /*?utm_source=
Disallow: /order/
Disallow: /*download*
Disallow: /test.php
Allow: /bitrix/components/
Allow: /bitrix/cache/
Allow: /bitrix/js/
Allow: /bitrix/templates/
Allow: /bitrix/panel/
Allow: /test/
Host: <?= $http ?>://<?= $host . PHP_EOL; ?>
Sitemap: <?= $http ?>://<?= $host; ?>/sitemap.xml
<?
if (count($subdomain) == 2) {
	echo 'User-Agent: Googlebot' . PHP_EOL;
	echo 'Disallow: /';
}
