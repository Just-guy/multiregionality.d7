<?
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;
// пространство имен для получения ID модуля
use Bitrix\Main\HttpApplication;
// пространство имен для загрузки необходимых файлов, классов, модулей
use Bitrix\Main\Loader;
// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// получение запроса из контекста для обработки данных
$request = HttpApplication::getInstance()->getContext()->getRequest();

// получаем id модуля
$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);

// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT < "S") {
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

// подключение модуля
Loader::includeModule($module_id);

// настройки модуля для админки в том числе значения по умолчанию
$aTabs = array(
	array(
		// значение будет вставленно во все элементы вкладки для идентификации (используется для javascript)
		"DIV" => "edit1",
		// название вкладки в табах 
		"TAB" => "Название вкладки в табах",
		// заголовок и всплывающее сообщение вкладки
		"TITLE" => "Главное название в админке",
		// массив с опциями секции
		"OPTIONS" => array(
			"Опции",
			array(
				// имя элемента формы, для хранения в бд
				"multiregionality_yandex_map_key_v3",
				// поясняющий текст
				"API ключ Яндекс Карт v3",
				// значение по умолчани, значение text по умолчанию "50"
				"",
				// тип элемента формы "text", ширина, высота
				array(
					"text",
					40,
					50
				)
			)
		)
	),
	array(
		// значение будет вставленно во все элементы вкладки для идентификации (используется для javascript)
		"DIV"   => "edit2",
		// название вкладки в табах из основного языкового файла битрикс
		"TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
		// заголовок и всплывающее сообщение вкладки из основного языкового файла битрикс
		"TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
	)
);

// проверяем текущий POST запрос и сохраняем выбранные пользователем настройки
if ($request->isPost() && check_bitrix_sessid()) {
	// цикл по вкладкам
	foreach ($aTabs as $aTab) {

		if (!is_array($aTab["OPTIONS"]) || !isset($aTab["OPTIONS"])) {
			continue;
		}

		// цикл по заполненым пользователем данным
		foreach ($aTab["OPTIONS"] as $arOption) {
			// если это название секции, переходим к следующий итерации цикла
			if (!is_array($arOption)) {
				continue;
			}
			// проверяем POST запрос, если инициатором выступила кнопка с name="Update" сохраняем введенные настройки в базу данных
			if ($request["Update"]) {
				// получаем в переменную $optionValue введенные пользователем данные
				$optionValue = $request->getPost($arOption[0]);
				// метод getPost() не работает с input типа checkbox, для работы сделал этот костыль
				if ($arOption[0] == "hmultiregionality_checkbox") {
					if ($optionValue == "") {
						$optionValue = "N";
					}
				}
				// устанавливаем выбранные значения параметров и сохраняем в базу данных, хранить можем только текст, значит если приходит массив, то разбиваем его через запятую, если не массив сохраняем как есть
				Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
			}
			// проверяем POST запрос, если инициатором выступила кнопка с name="default" сохраняем дефолтные настройки в базу данных 
			if ($request["default"]) {
				// устанавливаем дефолтные значения параметров и сохраняем в базу данных
				Option::set($module_id, $arOption[0], $arOption[2]);
			}
		}
	}
}

// отрисовываем форму, для этого создаем новый экземпляр класса CAdminTabControl, куда и передаём массив с настройками
$tabControl = new CAdminTabControl(
	"tabControl",
	$aTabs
);

// отображаем заголовки закладок
$tabControl->Begin();
?>

<form action="<? echo ($APPLICATION->GetCurPage()); ?>?mid=<? echo ($module_id); ?>&lang=<? echo (LANG); ?>" method="post">
	<? $tabControl->BeginNextTab(); ?>
	<? foreach ($aTabs as $aTab) {
		if ($aTab["OPTIONS"]) {
			// завершает предыдущую закладку, если она есть, начинает следующую
			// отрисовываем форму из массива
			__AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
		}
	} ?>
	<tr class="heading">
		<td colspan="3">Компоненты</td>
	</tr>
	<tr>
		<td colspan="3">
			<div class="hmultiregionality-tips">
				<div class="adm-info-message">
					<h2>Компонент выбора региона:</h2>
					$APPLICATION->IncludeComponent(<br>
						"multiregionality.d7:select.city",<br>
						"",<br>
					Array()<br>
					);<br>
				</div>
				<div class="adm-info-message">
					<h2>Компонент выбора контактных данных региона:</h2>
					$APPLICATION->IncludeComponent(<br>
						"multiregionality.d7:contact.details",<br>
						"",<br>
					Array()<br>
					);<br>
				</div>
				<div class="adm-info-message">
					<h2>Компонент вывода карты региона с метками:</h2>
					$APPLICATION->IncludeComponent(<br>
						"multiregionality.d7:delivery.region",<br>
						".default",<br>
						array(<br>
							"COMPONENT_TEMPLATE" => ".default",<br>
							"CARD_WIDTH" => "100%",<br>
							"CARD_HEIGHT" => "400px",<br>
							"ID_MAP" => "map-delivery-region"<br>
						),<br>
						false<br>
					);<br>
				</div>
			</div>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="3">Подсказки</td>
	</tr>
	<tr>
		<td colspan="3">
			<div class="hmultiregionality-tips">
				<div class="adm-info-message">
					<h2>Топонимы:</h2>
					<b>Название</b> - {city.name}<br>
					<b>Название в родительном падеже</b> - {city.name.genitive}<br>
					<b>Название в творительном падеже</b> - {city.name.instrumental}<br>
					<b>Название в предложном падеже</b> - {city.name.prepositional}<br>
					<b>Адрес</b> - {city.address}<br>
					<b>Телефон</b> - {city.telephone}<br>
					<b>E-mail</b> - {city.email}<br>
					<b>Описание относящееся к региону</b> - {city.text}<br>
				</div>
				<div class="adm-info-message">
					<h2>Динамический robots.txt:</h2>
					<b>1.</b> Формируем robots через стандартную возможность битрикса <a href="/bitrix/admin/seo_robots.php?lang=ru" target="_blank">тут!</a><br>
					<b>2.</b> Пользуемся файлом с динамической подстановкой <a href="/robots.php" target="_blank">robots.php</a><br>
				</div>
				<div class="adm-info-message">
					<h2>Динамический sitemap:</h2>
					<b>1.</b> Формируем sitemap через стандартную возможность битрикса <a href="http://localhost/bitrix/admin/seo_sitemap.php?lang=ru" target="_blank">тут!</a><br>
					<b>2.</b> Генерируем динамический sitemap — <a href="/sitemap_gen.php" target="_blank">sitemap_gen.php</a>.<br><b>ВАЖНО!</b> в момент генерации находится на главном домене без поддоменов<br>
					<b>3.</b> Пользуемся файлом с динамической подстановкой <a href="/sitemap.xml" target="_blank">sitemap.xml</a><br>
				</div>
				<div class="adm-info-message">
					<h2>Регистрируем API ключ для Яндекс Карт v3:</h2>
					<b>1.</b> Переходим в кабинет разработчика <a href="https://developer.tech.yandex.ru/services" target="_blank">https://developer.tech.yandex.ru/services</a>.<br>
					<b>2.</b> Подключаем API — «JavaScript API и HTTP Геокодер».
					<img src="/bitrix/modules/multiregionality.d7/install/images/yandex.developer.png" alt="Второй шаг" style="max-width: 100%"><br><br>
					<b>3.</b> Нажимаем на «JavaScript API и HTTP Геокодер».<br>
					<img src="/bitrix/modules/multiregionality.d7/install/images/yandex.developer_1.png" alt="Третий шаг" style="max-width: 100%"><br><br>
					<b>4.</b> Создаем новый ключ и нажимаем на «Изменить».<br>
					<img src="/bitrix/modules/multiregionality.d7/install/images/yandex.developer_2.png" alt="Четвертый шаг" style="max-width: 100%"><br><br>
					<b>5.</b> В поле «Ограничение по HTTP Referer» вводим наш домен.<br>
					<img src="/bitrix/modules/multiregionality.d7/install/images/yandex.developer_3.png" alt="Пятый шаг" style="max-width: 100%"><br><br>
					<b>5.</b> Используем на API ключ.<br>
					<img src="/bitrix/modules/multiregionality.d7/install/images/yandex.developer_4.png" alt="Шестой шаг" style="max-width: 100%">
				</div>
			</div>
		</td>
	</tr>
	<? // завершает предыдущую закладку, если она есть, начинает следующую
	$tabControl->BeginNextTab();
	// выводим форму управления правами в настройках текущего модуля
	require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php";
	// подключаем кнопки отправки формы
	$tabControl->Buttons();
	// выводим скрытый input с идентификатором сессии
	echo (bitrix_sessid_post());
	// выводим стандартные кнопки отправки формы
	?>
	<input class="adm-btn-save" type="submit" name="Update" value="Применить" />
	<input type="submit" name="default" value="По умолчанию" />
</form>
<style>
	.hmultiregionality-tips {
		display: flex;
		gap: 15px;
		flex-wrap: wrap;
	}

	.hmultiregionality-tips>.adm-info-message {
		flex: 1 1 calc((100% / 3) - 60px);
		margin: 0 !important;
	}
</style>
<?
// обозначаем конец отрисовки формы
$tabControl->End();
