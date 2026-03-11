/**
 * @fileoverview Модуль для работы с простыми операциями ККТ (контрольно-кассовой техники)
 * 
 * Этот модуль предоставляет интерфейс для выполнения базовых операций с ККТ:
 * - Открытие и закрытие смены
 * - Получение X-отчета и Z-отчета
 * - Проверка статуса оборудования
 * - Отображение информации о состоянии кассы в пользовательском интерфейсе
 * 
 * Для корректной работы модулю необходимо:
 * - Подключение функций: showOperationModal(), getCommandParams(), showStatusInfo(), showStatusError()
 * - Наличие справочника kktStatusFields с описанием полей статуса
 * - Наличие соответствующих элементов в HTML документе
 * 
 * @author Компания/Разработчик
 * @version 2.0
 */

/**
 * Выполняет операцию открытия смены на ККТ
 * Отправляет команду на сервер и отображает модальное окно с прогрессом операции
 * @function openShift
 * @returns {void}
 */
function openShift() {
	// Отображаем модальное окно с названием операции
	showOperationModal('Открытие смены');
	// Отправляем запрос параметров команды на сервер
	getCommandParams('openShift');
}

/**
 * Выполняет операцию получения X-отчета ККТ
 * X-отчет выводит информацию о текущем состоянии кассы без закрытия смены
 * @function XReport
 * @returns {void}
 */
function XReport() {
	// Отображаем модальное окно с названием операции
	showOperationModal('X-отчет');
	// Отправляем запрос параметров команды на сервер
	getCommandParams('XReport');
}

/**
 * Выполняет операцию закрытия смены (Z-отчет) на ККТ
 * Z-отчет закрывает текущую смену и подготавливает кассу к следующей смене
 * @function closeShift
 * @returns {void}
 */
function closeShift() {
	// Отображаем модальное окно с названием операции
	showOperationModal('Закрытие смены');
	// Отправляем запрос параметров команды на сервер
	getCommandParams('closeShift');
}

/**
 * Получает и отображает краткий статус ККТ
 * Выводит только основные поля информации о состоянии кассы
 * @function KKTStatus
 * @returns {void}
 */
function KKTStatus() {
	// Отображаем модальное окно с названием операции
	showOperationModal('Статус ККТ');
	// Отправляем запрос на получение статуса с передачей функции обработки результата
	getCommandParams('KKTStatus', {}, printKKTStatus);
}

/**
 * Получает и отображает полный статус ККТ со всеми деталями
 * Выводит расширенный набор параметров состояния кассы
 * @function KKTFullStatus
 * @returns {void}
 */
function KKTFullStatus() {
	// Отображаем модальное окно с названием операции
	showOperationModal('Полный статус ККТ');
	// Отправляем запрос на получение статуса с передачей функции обработки для полного отображения
	getCommandParams('KKTStatus', {}, printKKTFullStatus);
}
/**
 * Обрабатывает результат запроса статуса ККТ и выводит краткую информацию
 * Передает параметр для отображения только основных полей без расширенной информации
 * @function printKKTStatus
 * @param {Object} resultJson - Объект с ответом сервера, содержит свойство Info с данными статуса
 * @param {Object} resultJson.Info - Информация о статусе ККТ
 * @returns {void}
 */
function printKKTStatus(resultJson) {
	// Проверяем, что результат получен и содержит информацию
	if (!resultJson || !resultJson.Info) {
		console.error('Ошибка: Пустой результат при получении статуса ККТ');
		showStatusError('Ошибка при получении статуса ККТ');
		return;
	}
	// Вызываем функцию отображения с флагом краткого вывода (true = только основные поля)
	showKKTStatus(resultJson.Info, true);
}

/**
 * Обрабатывает результат запроса статуса ККТ и выводит полную информацию
 * Передает параметр для отображения всех доступных полей информации о кассе
 * @function printKKTFullStatus
 * @param {Object} resultJson - Объект с ответом сервера, содержит свойство Info с данными статуса
 * @param {Object} resultJson.Info - Полная информация о статусе ККТ
 * @returns {void}
 */
function printKKTFullStatus(resultJson) {
	// Проверяем, что результат получен и содержит информацию
	if (!resultJson || !resultJson.Info) {
		console.error('Ошибка: Пустой результат при получении полного статуса ККТ');
		showStatusError('Ошибка при получении полного статуса ККТ');
		return;
	}
	// Вызываем функцию отображения без флага (false = вывести все поля)
	showKKTStatus(resultJson.Info);
}
/**
 * Форматирует и выводит информацию о статусе ККТ в модальном окне
 * Преобразует данные статуса в HTML таблицу и отображает их пользователю
 * @function showKKTStatus
 * @param {Object} resultJson - Объект с параметрами статуса ККТ
 * @param {boolean} [ifShort=false] - Флаг краткого вывода: true = только основные поля, false = все поля
 * @returns {void}
 * @throws {Error} Если resultJson пуст или имеет неправильный формат
 */
function showKKTStatus(resultJson, ifShort = false) {
	// Проверяем наличие входных данных
	if (!resultJson || typeof resultJson !== 'object') {
		console.error('Ошибка: Невалидные данные статуса ККТ', resultJson);
		showStatusError('Ошибка обработки данных статуса ККТ');
		return;
	}

	// Инициализируем переменные для хранения HTML строк и текстовых значений
	let lines = []; // Массив для хранения HTML-строк таблицы
	let caption = ''; // Название (описание) поля
	let processFunc; // Функция обработки значения поля

	// Проверяем наличие справочника полей статуса
	if (!kktStatusFields || typeof kktStatusFields !== 'object') {
		console.error('Ошибка: Справочник полей kktStatusFields не определен');
		showStatusError('Ошибка: Конфигурация полей не загружена');
		return;
	}

	// Перебираем все доступные поля статуса из справочника
	for (let key in kktStatusFields) {
		// Проверяем, что это собственное свойство объекта, а не унаследованное
		if (!kktStatusFields.hasOwnProperty(key)) {
			continue;
		}

		// Если параметр не должен выводиться в кратком списке, и выбрано отображение краткого списка - то пропускаем
		if (ifShort && !kktStatusFields[key].short) {
			continue;
		}

		// Получаем функцию обработки для этого поля, или используем функцию идентичности
		processFunc = kktStatusFields[key].process || ((value) => value);

		// Получаем название (описание) поля из справочника
		caption = kktStatusFields[key].name || key;

		// Проверяем наличие значения в ответе сервера
		if (resultJson[key] !== undefined) {
			// Если значение NULL, пропускаем это поле (пустое значение)
			if (resultJson[key] === null) {
				continue;
			}

			// Если значение пустая строка, пропускаем это поле
			if (resultJson[key] === '') {
				continue;
			}

			// Если значение - логическое (boolean), преобразуем его в читаемую строку
			if (typeof resultJson[key] === 'boolean') {
				// true преобразуется в "Да", false - в "Нет"
				resultJson[key] = resultJson[key] ? 'Да' : 'Нет';
			}

			// Применяем функцию обработки к значению (например, форматирование дата/времени)
			const processedValue = processFunc(resultJson[key]);

			// Проверяем, что обработанное значение не пусто
			if (processedValue === null || processedValue === undefined || processedValue === '') {
				continue;
			}

			// Добавляем форматированную строку в массив для последующей сборки таблицы
			lines.push(
				`<li class="list-group-item list-group-item-info"><strong>${caption}:</strong> ${processedValue}</li>`,
			);
		}
	}

	// Проверяем, есть ли данные для отображения
	if (lines.length > 0) {
		// Если есть данные, собираем HTML список из всех строк
		const content = `<ul class="list-group list-group-flush">${lines.join('')}</ul>`;
		// Выводим информацию в модальное окно успешно
		showStatusInfo(content);
	} else {
		// Если нет данных, выводим сообщение об ошибке/отсутствии информации
		showStatusError('Нет данных для отображения');
	}
}
/**
 * Инициализация модуля при загрузке DOM дерева документа
 * Привязывает обработчики событий клика к кнопкам операций ККТ
 * Проверяет наличие всех необходимых DOM элементов перед привязкой
 */
document.addEventListener('DOMContentLoaded', function () {
	try {
		// Создаем объект с маппингом ID элементов на функции обработчиков
		const buttonHandlers = {
			'openShift': openShift,           // Кнопка открытия смены
			'closeShift': closeShift,         // Кнопка закрытия смены
			'XReport': XReport,               // Кнопка X-отчета
			'KKTStatus': KKTStatus,           // Кнопка краткого статуса
			'KKTFullStatus': KKTFullStatus    // Кнопка полного статуса
		};

		// Логируем начало инициализации для отладки
		console.log('Инициализация модуля простых операций...');

		// Перебираем все кнопки и привязываем обработчики
		for (const [elementId, handler] of Object.entries(buttonHandlers)) {
			// Ищем элемент в DOM по ID
			const element = document.getElementById(elementId);

			// Проверяем, что элемент найден в документе
			if (!element) {
				console.warn(`Предупреждение: Элемент с ID '${elementId}' не найден в документе`);
				continue; // Пропускаем этот элемент и переходим к следующему
			}

			// Проверяем, что это элемент кнопки или другого интерактивного элемента
			if (!element || typeof element.addEventListener !== 'function') {
				console.warn(`Предупреждение: Элемент '${elementId}' не поддерживает addEventListener`);
				continue; // Пропускаем этот элемент
			}

			// Добавляем обработчик события клика на кнопку
			element.addEventListener('click', handler);
			console.log(`Обработчик для '${elementId}' успешно привязан`);
		}

		console.log('Инициализация модуля завершена успешно');
	} catch (error) {
		// Выводим ошибку в консоль если что-то пошло не так
		console.error('Ошибка при инициализации модуля простых операций:', error);
	}
});
