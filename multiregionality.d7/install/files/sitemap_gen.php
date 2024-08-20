<? require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main\Loader;

Loader::includeModule('landing');
$host = \Bitrix\Main\Context::getCurrent()->getRequest()->getHttpHost();
$subdomain = explode('.', $host);
if(count($subdomain) == 2) $host = $subdomain[1];

function sitemap_gen($site_url)
{
	$sitemaps = glob($_SERVER['DOCUMENT_ROOT'] . '/sitemap*.xml');
	foreach ($sitemaps as $sitemap) {
		$error = [];
		$new_path = $_SERVER["DOCUMENT_ROOT"] . '/' . str_replace('.xml', '.php', basename($sitemap));
		$dyn_sitemapContent = '<?' . PHP_EOL . '$host = preg_replace("/\:\d+/is", "", $_SERVER["HTTP_HOST"]);' . PHP_EOL .
			'if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){' . PHP_EOL .
			'	$http = "https";' . PHP_EOL .
			'}' . PHP_EOL .
			'else{' . PHP_EOL .
			'	$http = "http";' . PHP_EOL .
			'}' . PHP_EOL .
			'header("Content-Type: text/xml");' . PHP_EOL;

		$sitemapContent = file_get_contents($sitemap);
		if (!$sitemapContent) {
			$error[basename($sitemap)] = 'Файл не найден или пустой.';
		}

		// замены
		$search = array(
			$site_url,
			'http:',
			'https:',
		);
		$replace = array(
			'<?=$host?>',
			'<?=$http?>:',
			'<?=$http?>:'
		);

		$sitemapContent = str_replace($search, $replace, $sitemapContent);

		$sitemapContent = preg_replace('/(\<\?xml[^\>]+\>)/i', "echo '$1';?>" . PHP_EOL, $sitemapContent);
		if (basename($sitemap) == 'sitemap.xml') {
			$sitemapContent = str_replace('.xml', '.php', $sitemapContent);
		}

		$dyn_sitemapContent .= $sitemapContent;
		if (!file_put_contents($new_path, $dyn_sitemapContent)) {
			$error[basename($new_path)] = 'Файл не удалось сохранить.';
		} else {
			unlink($sitemap);
		}
	}
	return count($error) > 0 ? $error : 'Динамическая карта сайта сгенерирована';
}
print_r(sitemap_gen($host));
