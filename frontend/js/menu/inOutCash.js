/**
 * Модуль для работы с модальным окном внесения/изъятия наличных.
 * Обрабатывает ввод суммы, валидацию и отправку команд на сервер.
 */

/**
 * @var {Modal} inOutCashModalObj - Объект модального окна Bootstrap для внесения/изъятия наличных
 */
let inOutCashModalObj = null;

/**
 * Получает и валидирует сумму из поля ввода.
 * Проверяет, что введено положительное число, и обновляет визуальную валидацию.
 *
 * @returns {number|boolean} Сумма в копейках (умноженная на 100) или false при ошибке
 */
function getAmount() {
	// получаем элемент поля суммы
	const amountInput = document.getElementById('cash-amount');
	if (!amountInput) {
		console.error('Элемент cash-amount не найден');
		return false;
	}

	// парсим значение как целое число
	const amount = parseInt(amountInput.value, 10);

	// очищаем предыдущие классы валидации
	amountInput.classList.remove('is-invalid', 'is-valid');

	// проверяем, что сумма является положительным числом
	if (isNaN(amount) || amount <= 0) {
		// добавляем класс ошибки
		amountInput.classList.add('is-invalid');
		return false;
	} else {
		// добавляем класс успеха
		amountInput.classList.add('is-valid');
		// возвращаем сумму в копейках
		return amount * 100;
	}
}

/**
 * Выполняет внесение наличных: валидирует сумму, скрывает модальное окно,
 * показывает модальное окно операции и отправляет команду.
 *
 * @returns {void}
 */
function depositCash() {
	// получаем сумму в копейках
	const amount = getAmount();
	if (!amount) {
		return; // если сумма невалидна, выходим
	}

	// скрываем модальное окно внесения/изъятия
	if (inOutCashModalObj) {
		inOutCashModalObj.hide();
	}

	// показываем модальное окно выполнения операции
	showOperationModal('Внесение наличных');

	// отправляем команду на сервер
	getCommandParams('depositCash', { amount });
}

/**
 * Выполняет изъятие наличных: валидирует сумму, скрывает модальное окно,
 * показывает модальное окно операции и отправляет команду.
 *
 * @returns {void}
 */
function paymentCash() {
	// получаем сумму в копейках
	const amount = getAmount();
	if (!amount) {
		return; // если сумма невалидна, выходим
	}

	// скрываем модальное окно внесения/изъятия
	if (inOutCashModalObj) {
		inOutCashModalObj.hide();
	}

	// показываем модальное окно выполнения операции
	showOperationModal('Изъятие наличных');

	// отправляем команду на сервер
	getCommandParams('paymentCash', { amount });
}

/**
 * Формирует строку параметров URL для запроса команды.
 *
 * @param {string} action - Название команды (например, 'depositCash')
 * @param {Object} additionalParams - Дополнительные параметры команды
 * @returns {string} Строка параметров URL
 */
function getUrlParams(action, additionalParams = {}) {
	// создаем объект URLSearchParams для удобного формирования параметров
	let params = new URLSearchParams();

	// добавляем основную команду
	params.append('command', action);

	// добавляем дополнительные параметры
	for (let key in additionalParams) {
		if (additionalParams.hasOwnProperty(key)) {
			params.append(key, additionalParams[key]);
		}
	}

	// добавляем информацию о кассире
	const cashierNameEl = document.getElementById('cashierName');
	if (cashierNameEl) {
		params.append('cashierName', cashierNameEl.textContent);
	}

	const cashierVatinEl = document.getElementById('cashierVatin');
	if (cashierVatinEl) {
		params.append('cashierVatin', cashierVatinEl.textContent);
	}

	// добавляем номер ККТ
	const kktNumberEl = document.getElementById('kktNumber');
	if (kktNumberEl) {
		params.append('kktNumber', kktNumberEl.textContent);
	}

	// добавляем уникальный ID команды
	params.append('idCommand', guid());

	// возвращаем строку параметров
	return params.toString();
}

/**
 * Получает параметры команды и отправляет GET-запрос на сервер.
 *
 * @param {string} action - Название команды
 * @param {Object} additionalParams - Дополнительные параметры
 * @param {Function|null} successCallback - Функция обратного вызова при успехе
 * @returns {void}
 */
function getCommandParams(action, additionalParams = {}, successCallback = null) {
	// формируем строку параметров
	let params = getUrlParams(action, additionalParams);

	// отправляем запрос на бэкенд
	myFetch(`../backend/Generator.php?${params}`, successCallback);
}

// инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function () {
    // находим элемент модального окна
    const inOutCashModal = document.getElementById('inOutCashModal');
    if (!inOutCashModal) {
			console.error('Модальное окно inOutCashModal не найдено');
			return;
		}

		// инициализируем объект модального окна Bootstrap
		inOutCashModalObj = new bootstrap.Modal(inOutCashModal);

		// находим кнопку операции
		const operationButton = document.getElementById('cash-operation-button');
		if (!operationButton) {
			console.error('Кнопка cash-operation-button не найдена');
			return;
		}

		// находим поле суммы
		const cashAmountInput = document.getElementById('cash-amount');
		if (!cashAmountInput) {
			console.error('Поле cash-amount не найдено');
			return;
		}

    // добавляем обработчик нажатия Enter в поле суммы
    cashAmountInput.addEventListener('keydown', (event) => {
			if (event.key === 'Enter') {
				event.preventDefault(); // предотвращаем стандартное поведение
				operationButton.click(); // имитируем клик по кнопке
			}
		});

    // обработчик перед показом модального окна
    inOutCashModal.addEventListener('show.bs.modal', (event) => {
			// получаем направление операции из data-атрибута кнопки
			const direction = event.relatedTarget?.dataset?.bsDirection;
			if (!direction) {
				console.error('Направление операции не указано');
				return;
			}

			// устанавливаем заголовок модального окна
			const modalLabel = document.getElementById('inOutCashModalLabel');
			if (modalLabel) {
				modalLabel.textContent =
					direction === 'in' ? 'Внесение наличных' : 'Изъятие наличных';
			}

			// устанавливаем заголовок поля суммы
			const amountLabel = document.getElementById('cash-amount-label');
			if (amountLabel) {
				amountLabel.textContent =
					direction === 'in' ? 'Сумма внесения' : 'Сумма изъятия';
			}

			// устанавливаем текст кнопки
			operationButton.textContent = direction === 'in' ? 'Внести' : 'Изъять';

			// сохраняем направление в data-атрибуте кнопки для использования в обработчике клика
			operationButton.dataset.direction = direction;
		});

    // обработчик после показа модального окна
    inOutCashModal.addEventListener('shown.bs.modal', (event) => {
			// устанавливаем фокус на поле суммы
			cashAmountInput.focus({ focusVisible: true });
		});

    // обработчик перед скрытием модального окна
    inOutCashModal.addEventListener('hide.bs.modal', (event) => {
			// очищаем поле суммы
			cashAmountInput.value = '';

			// очищаем классы валидации
			cashAmountInput.classList.remove('is-invalid', 'is-valid');
		});

    // единый обработчик клика по кнопке операции
    operationButton.addEventListener('click', () => {
			const direction = operationButton.dataset.direction;
			if (direction === 'in') {
				depositCash();
			} else if (direction === 'out') {
				paymentCash();
			} else {
				console.error('Неизвестное направление операции');
			}
		});
});
