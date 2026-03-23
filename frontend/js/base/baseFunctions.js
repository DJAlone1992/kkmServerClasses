// базовые функции для работы с сервером ККМ

/**
 * Обработчик ошибки по умолчанию.
 *
 * @param {string|Error} error - текст или объект ошибки
 */
function ExecuteError(error) {
	// показать сообщение об ошибке с запасным текстом
	showStatusError(error || 'Неизвестная ошибка');
}

/**
 * Обработчик успешного результата выполнения команды.
 *
 * @param {Object} result - объект ответа от сервера
 */
function ExecuteSuccess(result) {
	// выводим результат в консоль для отладки
	console.log('ExecuteSuccess result:', result);

	// проверка, что ответ является объектом
	if (typeof result !== 'object' || result === null) {
		showStatusError('Некорректный ответ от сервера');
		return;
	}

	// если статус ненулевой, считаем это ошибкой
	if (result.Status !== 0) {
		showStatusError(result.Error || 'Неизвестная ошибка');
		return;
	}

	// формируем сообщение об успехе
	const message = result.Message || 'Операция выполнена успешно';
	showStatusSuccess(message);

	// если сообщение было создано автоматически,
	// скрываем модальное окно через секунду
	if (!result.Message) {
		setTimeout(hideOperationModal, 1000);
	}
	//Запускаем получение параметров для экрана
	if (typeof screenParams == 'function') {
		screenParams();
	}
}

/**
 * Выполняет команду через нативный модуль KkmServer или
 * по HTTP, если модуль недоступен.
 *
 * @param {Object|string} Data - параметры команды (объект или JSON-строка)
 * @param {Function} [SuccessFunction=ExecuteSuccess] - функция при успешном ответе
 * @param {Function} [ErrorFunction=ExecuteError] - функция при ошибке
 * @param {number} [timeout=60000] - таймаут в миллисекундах для HTTP-запроса
 * @returns {Promise<*>|void} если путь HTTP, возвращает промис, иначе undefined
 */
async function ExecuteCommand(
	Data,
	SuccessFunction = ExecuteSuccess,
	ErrorFunction = ExecuteError,
	timeout = 60000,
) {
	// сначала пробуем выполнить команду через расширение
	try {
		if (typeof KkmServer !== 'undefined' && KkmServer !== null) {
			const payload = typeof Data === 'string' ? JSON.parse(Data) : Data;
			KkmServer.Execute(SuccessFunction, payload);
			return; // нативный путь не возвращает обещания
		}
	} catch (e) {
		// если что-то пошло не так с расширением,
		// переключаемся на HTTP
		console.warn('KkmServer execute failed, falling back to HTTP:', e);
	}

	// путь через HTTP

	// если в данных указан долгий таймаут, увеличим его
	if (Data && typeof Data === 'object' && Data.Timeout > 60) {
		timeout = (Data.Timeout + 20) * 1000;
	}

	// формируем базовый URL
	const base =
		UrlServer || `${window.location.protocol}//${window.location.host}/`;
	const Url = `${base.replace(/\/$/, '')}/Execute`;
	// заголовок авторизации, если пользователь/пароль заданы
	const authHeader =
		User || Password ? `Basic ${btoa(`${User}:${Password}`)}` : '';

	try {
		// выполняем fetch POST
		const res = await fetch(Url, {
			method: 'POST',
			mode: 'cors',
			cache: 'no-cache',
			headers: {
				'Content-Type': 'application/json; charset=UTF-8',
				...(authHeader && { Authorization: authHeader }),
			},
			redirect: 'follow',
			referrerPolicy: 'no-referrer',
			body: typeof Data === 'string' ? Data : JSON.stringify(Data),
		});

		// проверяем код ответа
		if (!res.ok) {
			const text = await res.text();
			throw new Error(`HTTP ${res.status}: ${text}`);
		}

		// парсим JSON и вызываем SuccessFunction
		const json = await res.json();
		SuccessFunction(json);
		return json;
	} catch (err) {
		// при ошибке вызываем ErrorFunction и отвергаем промис
		ErrorFunction(err.message || err);
		return Promise.reject(err);
	}
}

/**
 * Генерирует случайный GUID/UUID версии 4.
 * Используется простая математика, нельзя использовать для криптографии.
 * @returns {string} строка в формате xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
 */
function guid() {
	// вспомогательная функция возвращает 4 hex-символа
	const S4 = () => (((1 + Math.random()) * 0x10000) | 0).toString(16).slice(1);
	// собираем UUID из частей
	return `${S4()}${S4()}-${S4()}-${S4()}-${S4()}-${S4()}${S4()}${S4()}`;
}

/**
 * Упроститель для GET-запросов fetch с возможностью
 * автоматически выполнить команду из ответа.
 *
 * @param {string} url - адрес для запроса
 * @param {Function} successCallback - функция, вызываемая при успехе
 * @param {boolean} [execute=true] - если true, выполнит ExecuteCommand с командой из ответа
 * @param {Function} [errorCallback] - функция обработки ошибок
 * @returns {Promise<Object>|void} возвращает данные или промис
 */
async function myFetch(url, successCallback, execute = true, errorCallback) {
	url = backendDir.replace(/\/$/, '') + url;
	try {
		// делаем GET-запрос
		const res = await fetch(url, {
			method: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
			},
		});

		// если ответ не ок, обрабатываем ошибку
		if (!res.ok) {
			let message = res.statusText;
			try {
				// пытаемся прочитать JSON-ошибку
				const body = await res.json();
				message = `<strong>${body.errorCode}</strong> ${body.errorText}`;
			} catch (e) {
				// игнорируем ошибки парсинга
			}
			showStatusError(message);
			return;
		}
		if (successCallback === null || typeof successCallback !== 'function') {
			successCallback = ExecuteSuccess;
		}
		// парсим тело
		const data = await res.json();
		if (execute) {
			// если флаг execute, выполняем полученную команду
			ExecuteCommand(data.command, successCallback);
		} else {
			successCallback(data);
		}
		return data;
	} catch (err) {
		// при исключении вызываем callback ошибки, если есть,
		// иначе показываем стандартное сообщение
		if (typeof errorCallback === 'function') {
			errorCallback(err);
		} else {
			showStatusError(err.message || err);
		}
		console.error(err);
		return Promise.reject(err);
	}
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
		if (cashierNameEl.tagName === 'INPUT') {
			params.append('cashierName', cashierNameEl.value);
		} else {
			params.append('cashierName', cashierNameEl.textContent);
		}
	}

	const cashierVatinEl = document.getElementById('cashierVatin');
	if (cashierVatinEl) {
		if (cashierVatinEl.tagName === 'INPUT') {
			params.append('cashierVatin', cashierVatinEl.value);
		} else {
			params.append('cashierVatin', cashierVatinEl.textContent);
		}
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
	myFetch(`/Generator.php?${params}`, successCallback);
}