let currentShiftState = null;
let errorsInKkt = null;
document.addEventListener('DOMContentLoaded', function () {
	currentShiftState = document.getElementById('currentShiftState');
	errorsInKkt = document.getElementById('errorsInKkt');
});
function screenParams() {
	// Показываем индикатор загрузки
	if (currentShiftState) {
		currentShiftState.classList.remove('text-danger', 'text-success');
		currentShiftState.innerHTML =
			'<span class="spinner-grow spinner-grow-sm" role="status"></span>';
	}
	if (errorsInKkt) {
		errorsInKkt.classList.remove('text-danger', 'text-success');
		errorsInKkt.innerHTML =
			'<span class="spinner-grow spinner-grow-sm" role="status"></span>';
	}
	showOperationModal(
		'Проверка статуса',
		'Проверка состояния принтера перед печатью чека',
	);
	setTimeout(() => {
		getCommandParams('KKTStatus', {}, showScreenParams);
	}, 500);
}

function showScreenParams(resultJson) {
	// Проверяем наличие входных данных
	if (!resultJson || typeof resultJson !== 'object') {
		console.error('Ошибка: Невалидные данные статуса ККТ', resultJson);
		currentShiftState.innerHTML = 'Ошибка получения данных';
		errorsInKkt.innerHTML = 'Ошибка получения данных';
		return;
	}
	// Проверяем наличие справочника полей статуса
	if (!kktStatusFields || typeof kktStatusFields !== 'object') {
		console.error('Ошибка: Справочник полей kktStatusFields не определен');
		currentShiftState.innerHTML = 'Ошибка: Конфигурация полей не загружена';
		errorsInKkt.innerHTML = 'Ошибка: Конфигурация полей не загружена';
		return;
	}

	let processFunc; // Функция обработки значения поля
	let errors = false;
	let errorsList = [];
	// Перебираем все доступные поля статуса из справочника
	for (let key in kktStatusFields) {
		// Проверяем, что это собственное свойство объекта, а не унаследованное
		if (!kktStatusFields.hasOwnProperty(key)) {
			continue;
		}
		// Если параметр не должен выводиться в кратком списке, и выбрано отображение краткого списка - то пропускаем
		if (!kktStatusFields[key]?.error) {
			continue;
		}
		// Получаем функцию обработки для этого поля, или используем функцию идентичности
		processFunc = kktStatusFields[key].process || ((value) => value);

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
			if (
				processedValue === null ||
				processedValue === undefined ||
				processedValue === ''
			) {
				continue;
			}

			if (resultJson[key]?.error == true) {
				errors = errors || resultJson[key] == 'Да';
				if (resultJson[key] == 'Да') {
					errorsList.push(kktStatusFields[key].name);
				}
			}
		}
	}
	if (resultJson.Info.SessionState != 2) {
		errorsList.push(
			'Неверное состояние смены:' +
				getShiftStateName(resultJson.Info.SessionState),
		);
	}
	currentShiftState.innerHTML = getShiftStateName(resultJson.Info.SessionState);
	currentShiftState.classList.add(
		resultJson.Info.SessionState != 2 ? 'text-danger' : 'text-success',
	);

	errorsInKkt.innerHTML = errors ? 'Есть ошибки' : 'Нет ошибок';
	errorsInKkt.classList.add(errors ? 'text-danger' : 'text-success');

	if (errors || resultJson.Info.SessionState != 2) {
		showStatusError(errorsList.join('<br>'));
		enablePrinterCancelButton();
	} else {
		enablePrinterButtons();
		showStatusSuccess('Можно продолжить печать');
		hideOperationModal();
	}
}
