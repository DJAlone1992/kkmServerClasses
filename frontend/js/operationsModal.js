/**
 * Блок функций для отображения модального окна статуса работы с ККТ и запросами к серверу
 */

/**
 * Объект модального окна в bootstrap
 */
let myModal = null;

/**
 * Инициализация
 */
document.addEventListener('DOMContentLoaded', function () {
	// Инициализация модального окна
	myModal = new bootstrap.Modal(document.getElementById('operationStatus'));
	// Событие закрытия модального окна
	document
		.getElementById('operationStatus')
		.addEventListener('hidden.bs.modal', resetOperationModalState);
});
/**
 * Сброс состояния модального окна
 * @returns void
 */
function resetOperationModalState() {
	document.getElementById('operationStatusSpinner').style.visibility =
		'visible';
	document.getElementById('operationStatusPlaceholder').innerHTML = '';
	changeOperationModalText('operationStatusLabel', 'Обрабатываем запрос');
	disableButtons();
}

/**
 * Функция обновления текста в модальном окне
 * @param {String} position - id элемента
 * @param {String} text - текст
 * @returns void
 */
function changeOperationModalText(position, text) {
	if (typeof text != 'undefined' && text != null && text.length > 1) {
		document.getElementById(position).innerText = text;
	}
}

/**
 * Функция отображения модального окна
 * @param {String} pendingName - наименование действия, которое ожидается для выполнения
 * @param {String} caption - заголовок модального окна
 * @returns void
 */
function showOperationModal(pendingName = undefined, caption = undefined) {
	changeOperationModalText('operationStatusLabel', caption);
	changeOperationModalText('operationStatusName', pendingName);
	myModal.show();
}
/**
 * Функция скрытия модального окна
 * @returns void
 */
function hideOperationModal() {
	myModal.hide();
	resetOperationModalState();
}

/**
 * Функция добавления сообщения в модальное окно
 * @param {String} message - текст сообщения
 * @param {String} type - тип сообщения
 */
function appendAlert(message, type) {
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
	}
	const alertPlaceholder = document.getElementById(
		'operationStatusPlaceholder',
	);
	const wrapper = document.createElement('div');
	wrapper.innerHTML = [
		`<div class="alert alert-${type}" role="alert">`,
		`<h4 class="alert-heading">${header}</h4>`,
		'<hr>',
		`<div>${message}</div>`,
		'</div>',
	].join('');

	alertPlaceholder.append(wrapper);
}

/**
 * Отключение кнопок и блокировка выхода из модального окна
 *
 * @returns void
 */
function disableButtons() {
	const buttons = document.querySelectorAll('.operationStatusClose');
	for (let i = 0; i < buttons.length; i++) {
		buttons[i].disabled = true;
	}

	myModal._config.keyboard = false;
	myModal._config.backdrop = 'static';
}
/**
 * Включение кнопок и разблокировка выхода из модального окна
 *
 * @returns void
 */
function enableButtons() {
	const buttons = document.querySelectorAll('.operationStatusClose');
	for (let i = 0; i < buttons.length; i++) {
		buttons[i].disabled = false;
	}
	myModal._config.keyboard = true;
	myModal._config.backdrop = true;
}

/**
 * Вывод статуса "Успешно" в модальном окне
 * @param {String} content - текст сообщения
 * @returns void
 */
function showStatusSuccess(content) {
	appendAlert(content, 'success');
	document.getElementById('operationStatusSpinner').style.visibility = 'hidden';
	changeOperationModalText('operationStatusLabel', 'Результат запроса');
	enableButtons();
}
/**
 * Вывод статуса "Информация" в модальном окне
 * @param {String} content - текст сообщения
 * @returns void
 */
function showStatusInfo(content) {
	appendAlert(content, 'info');
	document.getElementById('operationStatusSpinner').style.visibility = 'hidden';
	changeOperationModalText('operationStatusLabel', 'Результат запроса');
	enableButtons();
}
/**
 * Вывод статуса "Ошибка" в модальном окне
 * @param {String} content - текст сообщения
 * @returns void
 */
function showStatusError(content) {
	appendAlert(content, 'danger');
	document.getElementById('operationStatusSpinner').style.visibility = 'hidden';
	changeOperationModalText('operationStatusLabel', 'Ошибка запроса');
	enableButtons();
}
