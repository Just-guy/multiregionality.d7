(function (window) {
	'use strict';

	window.JCMultiregionalityDeliveryRegion = function (arParams) {
		//debugger
		this.result = arParams;
		BX.ready(BX.delegate(this.init, this));
	}

	window.JCMultiregionalityDeliveryRegion.prototype = {
		init: function () {

			window.map = null;
			//debugger
			// Главная функция, вызывается при запуске скрипта
			main(this.result);
			async function main(params) {
				// ожидание загрузки модулей
				await ymaps3.ready;

				debugger
				const {
					YMap,
					YMapDefaultSchemeLayer,
					YMapControls,
					YMapDefaultFeaturesLayer,
					YMapMarker
				} = ymaps3;

				// Импорт модулей для элементов управления на карте
				const {
					YMapZoomControl,
					YMapGeolocationControl
				} = await ymaps3.import('@yandex/ymaps3-controls@0.0.1');
				//debugger
				// Координаты центра карты
				const CENTER_COORDINATES = params.CURRENT_REGION.COORDINATES;

				// Объект с параметрами центра и зумом карты
				const LOCATION = { center: CENTER_COORDINATES, zoom: (params.CURRENT_REGION.ZOOM != '') ? params.CURRENT_REGION.ZOOM : 10 };

				// Создание объекта карты
				map = new YMap(document.getElementById(params.PARAMS.ID_MAP), { location: LOCATION });

				// Добавление слоев на карту
				map.addChild(new YMapDefaultSchemeLayer());
				map.addChild(new YMapDefaultFeaturesLayer());


				for (var i in params.CURRENT_REGION.LIST_SETTLEMENTS_IN_REGION) {
					//debugger
					let element = params.CURRENT_REGION.LIST_SETTLEMENTS_IN_REGION[i];
					// Создание маркера
					const el = document.createElement('img');
					el.className = 'multiregionality-marker-img';
					el.src = params.TEMPLATE_PATH + '/images//marker.svg';
					el.title = element.SETTLEMENTS;

					// Создание заголовка маркера
					const markerTitle = document.createElement('div');
					markerTitle.className = 'multiregionality-marker-title';
					markerTitle.innerHTML = element.SETTLEMENTS;

					// Контейнер для элементов маркера
					const imgContainer = document.createElement('div');
					imgContainer.className = 'multiregionality-marker';
					imgContainer.appendChild(el);
					imgContainer.appendChild(markerTitle);

					// Добавление маркера на карту
					map.addChild(new YMapMarker({ coordinates: element.COORDINATES_SETTLEMENT }, imgContainer));
				};
			}
		},
	}
})(window)
