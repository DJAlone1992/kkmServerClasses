/**
 * @var {Modal} $inOutCashModalObj - Объект модального окна внесения/изъятия наличных
 */
let inOutCashModalObj = null;

/**
 * Функция получения суммы
 * @return {Number|Boolean}
 */
function getAmount() {
	const amount = parseInt(document.getElementById('cash-amount').value);
	if (isNaN(amount) || !amount) {
		document.getElementById('cash-amount').classList.add('is-invalid');
		return false;
	} else {
		document.getElementById('cash-amount').classList.add('is-valid');
		return amount;
	}
}
/**
 * Функция внесения наличных
 * @return {void}
 */
function depositCash() {
	const amount = getAmount();
	if (!amount) {
		return;
	}
	inOutCashModalObj.hide();

	showOperationModal('Внесение наличных');
	getCommandParams('depositCash', { amount: amount * 100 });
}
/**
 * Функция изъятия наличных
 * @return {void}
 */
function paymentCash() {
	const amount = getAmount();
	if (!amount) {
		return;
	}
	inOutCashModalObj.hide();

	showOperationModal('Изъятие наличных');
	getCommandParams('paymentCash', { amount: amount * 100 });
}
/**
 * Функция получения параметров для запроса
 * @param {String} action - наименование команды
 * @param {Object} additionalParams - дополнительные параметры
 * @return {String}
 */
function getUrlParams(action, additionalParams = {}) {
	let params = new URLSearchParams();
	params.append('command', action);
	for (let key in additionalParams) {
		params.append(key, additionalParams[key]);
	}
	params.append(
		'cashierName',
		document.getElementById('cashierName').textContent,
	);
	params.append(
		'cashierVatin',
		document.getElementById('cashierVatin').textContent,
	);
	params.append('kktNumber', document.getElementById('kktNumber').textContent);
	params.append('idCommand', guid());
	return params.toString();
}
function getCommandParams(
	action,
	additionalParams = {},
	successCallback = null,
) {
	let params = getUrlParams(action, additionalParams);
	myFetch(`../backend/Generator.php?${params}`, successCallback);
}

document.addEventListener('DOMContentLoaded', function () {
	// Инициализируем модальное окно внесения/изъятия наличных
	const inOutCashModal = document.getElementById('inOutCashModal');
	inOutCashModalObj = new bootstrap.Modal(inOutCashModal);

	if (inOutCashModal) {
		//Получаем кнопку запуска операции
		const operationButton = document.getElementById('cash-operation-button');

		//Событие нажатия Enter в поле суммы
		document
			.getElementById('cash-amount')
			.addEventListener('keydown', (event) => {
				if (event.key === 'Enter') {
					event.preventDefault();
					operationButton.click();
				}
			});
		//Событие открытия модального окна
		inOutCashModal.addEventListener('show.bs.modal', (event) => {
			//Получаем направление операции
			const direction = event.relatedTarget.dataset.bsDirection;
			//Устанавливаем заголовок модального окна
			document.getElementById('inOutCashModalLabel').textContent =
				direction === 'in' ? 'Внесение наличных' : 'Изъятие наличных';
			//Устанавливаем заголовок поля суммы
			document.getElementById('cash-amount-label').textContent =
				direction === 'in' ? 'Сумма внесения' : 'Сумма изъятия';

			//Устанавливаем текст кнопки
			operationButton.textContent = direction === 'in' ? 'Внести' : 'Изъять';
			//Устанавливаем событие нажатия на кнопку
			if (direction === 'in') {
				operationButton.addEventListener('click', depositCash);
			} else {
				operationButton.addEventListener('click', paymentCash);
			}
		});
		//Событие после открытия модального окна
		inOutCashModal.addEventListener('shown.bs.modal', (event) => {
			document.getElementById('cash-amount').focus({ focusVisible: true });
		});
		//Событие закрытия модального окна
		inOutCashModal.addEventListener('hide.bs.modal', (event) => {
			//Очищаем поле суммы
			document.getElementById('cash-amount').value = '';
			//Очищаем классы валидации поля суммы
			document
				.getElementById('cash-amount')
				.classList.remove('is-invalid', 'is-valid');

			//Удаляем событие нажатия на кнопку
			operationButton.removeEventListener('click', depositCash);
			operationButton.removeEventListener('click', paymentCash);
		});
	}
});
