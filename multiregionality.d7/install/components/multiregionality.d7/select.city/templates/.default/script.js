(function (window) {
	'use strict';

	window.JCMultiregionalitySelectCity = function (arParams) {
		//debugger
		this.result = arParams;
		this.showListButtonNode = document.querySelector('.' + arParams.SHOW_LIST_BUTTON_CLASS);
		this.listCityNode = document.querySelector('.' + arParams.LIST_CITY_CLASS);
		BX.ready(BX.delegate(this.init, this));
	}

	window.JCMultiregionalitySelectCity.prototype = {
		init: function () {
			//debugger
			BX.bind(this.showListButtonNode, 'click', BX.delegate(this.showList, this));
		},

		showList: function () {
			//debugger
			this.listCityNode.classList.toggle('block-cities__list-city_active');
		}
	}
})(window)
