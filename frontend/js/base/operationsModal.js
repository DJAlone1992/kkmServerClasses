/**
 * Блок функций для отображения модального окна статуса работы с ККТ и запросами к серверу
 */

/**
 * Объект модального окна в bootstrap
 */
let myModal = null;
let eventAppeared = false;
/**
 * Инициализация при загрузке DOM
 */
document.addEventListener('DOMContentLoaded', function () {
	// Получаем элемент модального окна по ID
	const modalElement = document.getElementById('operationStatus');
	// Проверяем, существует ли элемент
	if (!modalElement) {
		console.error('Модальное окно с ID "operationStatus" не найдено.');
		return;
	}
	// Инициализируем модальное окно Bootstrap
	myModal = new bootstrap.Modal(modalElement);
	// Добавляем слушатель события скрытия модального окна
	modalElement.addEventListener('hidden.bs.modal', resetOperationModalState);
	// Добавляем слушатель события показа модального окна
	modalElement.addEventListener('shown.bs.modal', function () {
		setTimeout(() => {
			if (eventAppeared) { return; }
			showStatusInfo('Похоже что приложение связи с ФР не отвечает. Перезапустите операцию')
		}, 10000);
	});
});

/**
 * Сброс состояния модального окна к начальному
 * @returns {void}
 */
function resetOperationModalState() {
	// Получаем элемент спиннера
	const spinner = document.getElementById('operationStatusSpinner');
	// Проверяем, существует ли элемент
	if (spinner) {
		// Устанавливаем видимость спиннера
		spinner.style.visibility = 'visible';
	}
	// Получаем элемент для сообщений
	const placeholder = document.getElementById('operationStatusPlaceholder');
	// Проверяем, существует ли элемент
	if (placeholder) {
		// Очищаем содержимое
		placeholder.innerHTML = '';
	}
	// Меняем текст заголовка
	changeOperationModalText('operationStatusLabel', 'Обрабатываем запрос');
	// Отключаем кнопки
	disableButtons();
}

/**
 * Функция обновления текста в модальном окне
 * @param {string} position - ID элемента для изменения текста
 * @param {string} text - Новый текст для установки
 * @returns {void}
 */
function changeOperationModalText(position, text) {
	// Проверяем, что text определен, не null и имеет длину больше 1
	if (typeof text !== 'undefined' && text != null && text.length > 1) {
		// Получаем элемент по ID
		const element = document.getElementById(position);
		// Проверяем, существует ли элемент
		if (element) {
			// Устанавливаем новый текст
			element.innerText = text;
		} else {
			console.warn(`Элемент с ID "${position}" не найден.`);
		}
	}
}

/**
 * Функция отображения модального окна
 * @param {string} [pendingName] - Наименование действия, которое ожидается для выполнения
 * @param {string} [caption] - Заголовок модального окна
 * @returns {void}
 */
function showOperationModal(pendingName = undefined, caption = undefined) {
	eventAppeared = false;
	// Меняем текст заголовка
	changeOperationModalText('operationStatusLabel', caption);
	// Меняем текст наименования операции
	changeOperationModalText('operationStatusName', pendingName);
	// Проверяем, инициализировано ли модальное окно
	if (myModal) {
		// Показываем модальное окно
		myModal.show();
	} else {
		console.error('Модальное окно не инициализировано.');
	}
}

/**
 * Функция скрытия модального окна
 * @returns {void}
 */
function hideOperationModal() {
	eventAppeared = false;
	// Проверяем, инициализировано ли модальное окно
	if (myModal) {
		// Скрываем модальное окно
		myModal.hide();
		// Сбрасываем состояние
		resetOperationModalState();
	} else {
		console.error('Модальное окно не инициализировано.');
	}
}

/**
 * Функция добавления сообщения в модальное окно
 * @param {string} message - Текст сообщения
 * @param {string} type - Тип сообщения (success, info, danger)
 */
function appendAlert(message, type) {
	eventAppeared = true;
	// Определяем заголовок в зависимости от типа
	let header = '';
	switch (type) {
		case 'success':
			header = 'Успешно!';
			break;
		case 'info':
			header = 'Информация';
			break;
		default:
			header = 'Ошибка!';
			type = 'danger'; // Устанавливаем тип по умолчанию
	}
	// Получаем элемент для сообщений
	const alertPlaceholder = document.getElementById('operationStatusPlaceholder');
	// Проверяем, существует ли элемент
	if (!alertPlaceholder) {
		console.error('Элемент с ID "operationStatusPlaceholder" не найден.');
		return;
	}
	// Создаем элемент для сообщения
	const wrapper = document.createElement('div');
	// Формируем HTML для алерта
	wrapper.innerHTML = [
		`<div class="alert alert-${type}" role="alert">`,
		`<h4 class="alert-heading">${header}</h4>`,
		'<hr>',
		`<div>${message}</div>`,
		'</div>',
	].join('');
	// Добавляем алерт в плейсхолдер
	alertPlaceholder.append(wrapper);
}

/**
 * Отключение кнопок и блокировка выхода из модального окна
 * @returns {void}
 */
function disableButtons() {
	// Получаем все кнопки с классом operationStatusClose
	const buttons = document.querySelectorAll('.operationStatusClose');
	// Проходим по всем кнопкам
	for (let i = 0; i < buttons.length; i++) {
		// Отключаем кнопку
		buttons[i].disabled = true;
	}
	// Проверяем, инициализировано ли модальное окно
	if (myModal) {
		// Отключаем клавиатуру и статический бэкдроп
		myModal._config.keyboard = false;
		myModal._config.backdrop = 'static';
	}
}

/**
 * Включение кнопок и разблокировка выхода из модального окна
 * @returns {void}
 */
function enableButtons() {
	// Получаем все кнопки с классом operationStatusClose
	const buttons = document.querySelectorAll('.operationStatusClose');
	// Проходим по всем кнопкам
	for (let i = 0; i < buttons.length; i++) {
		// Включаем кнопку
		buttons[i].disabled = false;
	}
	// Проверяем, инициализировано ли модальное окно
	if (myModal) {
		// Включаем клавиатуру и обычный бэкдроп
		myModal._config.keyboard = true;
		myModal._config.backdrop = true;
	}
}

/**
 * Вывод статуса "Успешно" в модальном окне
 * @param {string} content - Текст сообщения об успехе
 * @returns {void}
 */
function showStatusSuccess(content) {
	// Добавляем алерт успеха
	appendAlert(content, 'success');
	// Получаем элемент спиннера
	const spinner = document.getElementById('operationStatusSpinner');
	// Проверяем, существует ли элемент
	if (spinner) {
		// Скрываем спиннер
		spinner.style.visibility = 'hidden';
	}
	// Меняем текст заголовка
	changeOperationModalText('operationStatusLabel', 'Результат запроса');
	// Включаем кнопки
	enableButtons();
}

/**
 * Вывод статуса "Информация" в модальном окне
 * @param {string} content - Текст информационного сообщения
 * @returns {void}
 */
function showStatusInfo(content) {
	// Добавляем алерт информации
	appendAlert(content, 'info');
	// Получаем элемент спиннера
	const spinner = document.getElementById('operationStatusSpinner');
	// Проверяем, существует ли элемент
	if (spinner) {
		// Скрываем спиннер
		spinner.style.visibility = 'hidden';
	}
	// Меняем текст заголовка
	changeOperationModalText('operationStatusLabel', 'Результат запроса');
	// Включаем кнопки
	enableButtons();
}

/**
 * Вывод статуса "Ошибка" в модальном окне
 * @param {string} content - Текст сообщения об ошибке
 * @returns {void}
 */
function showStatusError(content) {
	// Добавляем алерт ошибки
	appendAlert(content, 'danger');
	// Получаем элемент спиннера
	const spinner = document.getElementById('operationStatusSpinner');
	// Проверяем, существует ли элемент
	if (spinner) {
		// Скрываем спиннер
		spinner.style.visibility = 'hidden';
	}
	// Меняем текст заголовка
	changeOperationModalText('operationStatusLabel', 'Ошибка запроса');
	// Включаем кнопки
	enableButtons();
}
