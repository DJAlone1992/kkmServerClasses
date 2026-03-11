/**
 * Функции выбора ККТ из списка доступных.
 */

/**
 * @var {String} kktNumber - номер ККТ
 */
let kktNumber = null;

/**
 * Инициализация
 */
document.addEventListener('DOMContentLoaded', function () {
	getActiveKKt();
	document.getElementById('kktNumber').addEventListener('click', changeKkt);
});

/**
 * Получение использующегося ККТ
 *
 * Делает запрос к серверу, для получения сохраненного номера из сессии пользователя.
 * Если в сессии нет сохраненного номера ККТ, то вызывает механизм выбора активного ККТ
 * Если в сессии есть номер ККТ, то устанавливает его как активный
 * @returns void
 */
function getActiveKKt() {
	myFetch(
		'../backend/SessionSaver.php?action=get',
		function (response) {
			if (response.error) {
				showOperationModal('Загружаем список ККТ', 'Требуется указать ККТ');
				let params = new URLSearchParams();
				params.append('command', 'DeviceList');
				params.append('cashierName', ' ');
				params.append('cashierVatin', ' ');
				params.append('kktNumber', ' ');
				params.append('idCommand', guid());
				myFetch(
					'../backend/Generator.php?' + params.toString(),
					showKktSwitcher,
				);
			} else {
				applyKktNumber(response);
			}
		},
		false,
	);
}
/**
 * Установка номера ККТ как активного
 * @param {Object} response - ответ сервера
 * @returns void
 */
function applyKktNumber(response) {
	showStatusSuccess('Номер присвоен');
	kktNumber = response.kktNumber;
	document.getElementById('kktNumber').innerText = response.kktNumber;
	setTimeout(hideOperationModal, 1000);
}

/**
 * Вывод модального окна выбора ККТ
 * @param {Object} resultJson - ответ сервера
 * @returns void
 */
function showKktSwitcher(resultJson) {
	let kktCount = 0;
	let kktList = [];
	// В ответе от сервера получаем список всех настроенных устройств, отделяем только те, что имеют тип "Фискальный регистратор"
	for (let i = 0; i < resultJson?.ListUnit?.length; i++) {
		let item = resultJson.ListUnit[i];
		if (item?.TypeDevice == 'Фискальный регистратор') {
			kktCount++;
			kktList.push(item.KktNumber);
		}
	}
	// Если не найдено ни одного ККТ, то выводим ошибку
	if (kktCount < 1) {
		showStatusError('Алгоритм взаимодействия с ККТ не настроен!');
		return;
	}
	// Если найден только один ККТ, то сразу его выбираем
	if (1 == kktCount) {
		kktNumber = kktList[0];
		myFetch(
			'../backend/SessionSaver.php?action=set&kktNumber=' + kktNumber,
			applyKktNumber,
			false,
		);
		return;
	}
	/*
	 * Если найдено больше одного ККТ, то выводим список
	 */
	//Создаем список
	const list = document.createElement('ul');
	list.classList.add('list-group');
	list.classList.add('list-group-flush');
	list.innerHTML = '';
	// Добавляем ссылки на выбор ККТ
	for (let i = 0; i < kktList.length; i++) {
		let line = switcherKktLink(kktList[i]);
		list.append(line);
	}
	// Выводим список в модальное окно
	showStatusInfo(list.outerHTML);
	// Меняем текст в модальном окне
	changeOperationModalText('operationStatusLabel', 'Выберите используемый ККТ');
	// Отключаем кнопки
	disableButtons();

	// Добавляем обработчик клика на ссылки
	let aList = document.querySelectorAll('.li-choose-kkt');
	for (let a in aList) {
		aList[a].addEventListener('click', chooseKktClick);
	}
}
/**
 * Создание ссылки на выбор ККТ
 *
 * @param {String} kktNumber
 * @returns {HTMLElement}
 */
function switcherKktLink(kktNumber) {
	let line = document.createElement('li');
	let a = document.createElement('a');
	a.innerText = kktNumber;
	line.classList.add('list-group-item');
	line.classList.add('list-group-item-info');
	line.innerText = 'Выбрать активным ККТ с номером: ';
	a.classList.add('li-choose-kkt');
	line.append(a);
	return line;
}

/**
 * Установка выбранного ККТ как текущего
 *
 * @param {Event} event - событие клика
 * @returns void
 */
function chooseKktClick(event) {
	myFetch(
		'../backend/SessionSaver.php?action=set&kktNumber=' +
			event.target.innerText,
		applyKktNumber,
		false,
	);
}
/**
 * Функция смены активного ККТ
 *
 * @returns void
 */
function changeKkt() {
	myFetch(
		'../backend/SessionSaver.php?action=clear',
		function (response) {
			kktNumber = null;
			document.getElementById('kktNumber').innerHTML =
				'<span class="spinner-grow spinner-grow-sm" role="status"></span>';
			getActiveKKt();
		},
		false,
	);
}
