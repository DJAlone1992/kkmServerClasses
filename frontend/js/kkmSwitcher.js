/**
 * Модуль для выбора и управления активной контрольно-кассовой техникой (ККТ).
 * Предоставляет функции для получения списка доступных ККТ, выбора активной ККТ
 * и сохранения выбора в сессии пользователя.
 */

/**
 * Глобальная переменная для хранения номера активной ККТ.
 * @type {string|null}
 */
let kktNumber = null;

/**
 * Константы для типов устройств и команд.
 */
const DEVICE_TYPE_FISCAL_REGISTRAR = 'Фискальный регистратор';
const COMMAND_DEVICE_LIST = 'DeviceList';

/**
 * Инициализация модуля при загрузке DOM.
 * Добавляет обработчики событий для элементов интерфейса.
 */
document.addEventListener('DOMContentLoaded', function () {
	// Получаем активную ККТ при загрузке страницы
	getActiveKKt();
	// Добавляем обработчик клика на кнопку смены ККТ
	const kktNumberElement = document.getElementById('kktNumber');
	if (kktNumberElement) {
		kktNumberElement.addEventListener('click', changeKkt);
	}
});

/**
 * Получает номер активной ККТ из сессии пользователя.
 * Если в сессии нет сохраненного номера, показывает интерфейс выбора ККТ.
 * Если номер есть, применяет его.
 *
 * @returns {void}
 */
function getActiveKKt() {
	// Отправляем запрос на получение сохраненного номера ККТ из сессии
	myFetch(
		'../backend/SessionSaver.php?action=get',
		function (response) {
			// Проверяем, есть ли ошибка в ответе (нет сохраненного номера)
			if (response.error) {
				// Показываем модальное окно с сообщением о загрузке списка ККТ
				showOperationModal('Загружаем список ККТ', 'Требуется указать ККТ');
				// Создаем параметры для запроса списка устройств

				let params = getUrlParams(COMMAND_DEVICE_LIST);
				// Отправляем запрос на получение списка устройств
				myFetch(`../backend/Generator.php?${params}`, showKktSwitcher);
			} else {
				// Если номер сохранен, применяем его
				applyKktNumber(response);
			}
		},
		false,
	);
}

/**
 * Применяет полученный номер ККТ как активный.
 * Обновляет интерфейс и скрывает модальное окно.
 *
 * @param {Object} response - Ответ сервера с номером ККТ
 * @param {string} response.kktNumber - Номер ККТ
 * @returns {void}
 */
function applyKktNumber(response) {
	// Проверяем, что ответ содержит номер ККТ
	if (!response || !response.kktNumber) {
		showStatusError('Ошибка: не получен номер ККТ');
		return;
	}
	// Показываем сообщение об успешном присвоении номера
	showStatusSuccess('Номер присвоен');
	// Сохраняем номер ККТ в глобальной переменной
	kktNumber = response.kktNumber;
	// Обновляем текст в интерфейсе
	const kktNumberElement = document.getElementById('kktNumber');
	if (kktNumberElement) {
		kktNumberElement.innerText = response.kktNumber;
	}
	// Скрываем модальное окно через 1 секунду
	setTimeout(hideOperationModal, 1000);
}

/**
 * Отображает модальное окно выбора ККТ из списка доступных устройств.
 * Фильтрует устройства по типу "Фискальный регистратор".
 * Если найдена только одна ККТ, автоматически выбирает её.
 * Если несколько - показывает список для выбора.
 *
 * @param {Object} resultJson - Ответ сервера со списком устройств
 * @param {Array} resultJson.ListUnit - Массив устройств
 * @returns {void}
 */
function showKktSwitcher(resultJson) {
	// Проверяем структуру ответа
	if (!resultJson || !resultJson.ListUnit || !Array.isArray(resultJson.ListUnit)) {
		showStatusError('Ошибка: некорректный ответ сервера');
		return;
	}

	let kktCount = 0; // Счетчик найденных ККТ
	let kktList = []; // Массив номеров ККТ

	// Перебираем все устройства и ищем фискальные регистраторы
	for (let i = 0; i < resultJson.ListUnit.length; i++) {
		let item = resultJson.ListUnit[i];
		// Проверяем тип устройства
		if (item && item.TypeDevice === DEVICE_TYPE_FISCAL_REGISTRAR) {
			kktCount++;
			kktList.push(item.KktNumber);
		}
	}

	// Если не найдено ни одного ККТ, показываем ошибку
	if (kktCount < 1) {
		showStatusError('Алгоритм взаимодействия с ККТ не настроен!');
		return;
	}

	// Если найден только один ККТ, автоматически выбираем его
	if (kktCount === 1) {
		kktNumber = kktList[0];
		myFetch(
			'../backend/SessionSaver.php?action=set&kktNumber=' + encodeURIComponent(kktNumber),
			applyKktNumber,
			false,
		);
		return;
	}

	// Если найдено несколько ККТ, показываем список для выбора
	const list = document.createElement('ul');
	list.classList.add('list-group');
	list.classList.add('list-group-flush');
	list.innerHTML = '';

	// Добавляем элементы списка для каждой ККТ
	for (let i = 0; i < kktList.length; i++) {
		let line = createKktSelectionLink(kktList[i]);
		list.append(line);
	}

	// Выводим список в модальное окно
	showStatusInfo(list.outerHTML);
	// Меняем текст в модальном окне
	changeOperationModalText('operationStatusLabel', 'Выберите используемый ККТ');
	// Отключаем кнопки управления
	disableButtons();

	// Добавляем обработчики клика на ссылки выбора ККТ
	const aList = document.querySelectorAll('.li-choose-kkt');
	for (let a of aList) {
		a.addEventListener('click', chooseKktClick);
	}
}

/**
 * Создает элемент списка с ссылкой для выбора ККТ.
 *
 * @param {string} kktNumber - Номер ККТ
 * @returns {HTMLElement} Элемент списка с ссылкой
 */
function createKktSelectionLink(kktNumber) {
	// Создаем элемент списка
	let line = document.createElement('li');
	line.classList.add('list-group-item');
	line.classList.add('list-group-item-info');
	line.innerText = 'Выбрать активным ККТ с номером: ';

	// Создаем ссылку для выбора
	let a = document.createElement('a');
	a.innerText = kktNumber;
	a.classList.add('li-choose-kkt');
	a.href = '#'; // Предотвращаем переход по ссылке
	a.style.cursor = 'pointer'; // Указываем, что это кликабельный элемент

	// Добавляем ссылку к элементу списка
	line.append(a);
	return line;
}

/**
 * Обработчик клика по ссылке выбора ККТ.
 * Сохраняет выбранный номер ККТ в сессии.
 *
 * @param {Event} event - Событие клика
 * @returns {void}
 */
function chooseKktClick(event) {
	event.preventDefault(); // Предотвращаем стандартное поведение ссылки
	const selectedKktNumber = event.target.innerText;
	if (!selectedKktNumber) {
		showStatusError('Ошибка: не удалось получить номер ККТ');
		return;
	}
	// Отправляем запрос на сохранение выбранного номера ККТ
	myFetch(
		'../backend/SessionSaver.php?action=set&kktNumber=' + encodeURIComponent(selectedKktNumber),
		applyKktNumber,
		false,
	);
}

/**
 * Функция смены активного ККТ.
 * Очищает сохраненный номер ККТ и запускает процесс выбора заново.
 *
 * @returns {void}
 */
function changeKkt() {
	// Отправляем запрос на очистку сохраненного номера ККТ
	myFetch(
		'../backend/SessionSaver.php?action=clear',
		function (response) {
			// Очищаем глобальную переменную
			kktNumber = null;
			// Показываем индикатор загрузки
			const kktNumberElement = document.getElementById('kktNumber');
			if (kktNumberElement) {
				kktNumberElement.innerHTML = '<span class="spinner-grow spinner-grow-sm" role="status"></span>';
			}
			// Запускаем процесс получения активной ККТ заново
			getActiveKKt();
		},
		false,
	);
}
