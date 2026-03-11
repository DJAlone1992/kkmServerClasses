
/**
 * Применяет слушатель события ввода к полю полученных наличных для расчета сдачи.
 */
function applyCashBackListener() {
	const totalReceivedCash = document.getElementById('totalReceivedCash');
	if (!totalReceivedCash) {
		console.error('Элемент с id "totalReceivedCash" не найден.');
		return;
	}
	totalReceivedCash.addEventListener('input', function () {
		// Получаем значение введенных наличных
		const totalReceivedCashValue = parseFloat(totalReceivedCash.value);
		// Получаем общую сумму наличными в чеке
		const totalCashElement = document.getElementById('totalCash');
		if (!totalCashElement) {
			console.error('Элемент с id "totalCash" не найден.');
			return;
		}
		const totalAmount = parseFloat(totalCashElement.value);
		// Получаем элемент для отображения сдачи
		const change = document.getElementById('totalBackCash');
		if (!change) {
			console.error('Элемент с id "totalBackCash" не найден.');
			return;
		}
		if (isNaN(totalReceivedCashValue)) {
			// Если введено не число, устанавливаем сдачу в 0.00
			change.value = '0.00';
		} else {
			// Рассчитываем сдачу
			const changeValue = totalReceivedCashValue - totalAmount;
			change.value = changeValue.toFixed(2);
			if (changeValue < 0) {
				// Если сдача отрицательная, выделяем красным
				change.classList.add('text-danger');
				change.classList.remove('text-success');
			} else {
				// Если сдача положительная или нулевая, выделяем зеленым
				change.classList.add('text-success');
				change.classList.remove('text-danger');
			}
		}
	});
}


/**
 * Форматирует значения в элементах с классом formatValue.
 * Применяет форматирование к input и текстовым элементам.
 */
function FormatValues() {
	const formatValueElements = document.querySelectorAll('.formatValue');
	formatValueElements.forEach(function (element) {
		let value;
		if (element.tagName.toLowerCase() === 'input') {
			// Для input элементов берем значение из value
			value = parseFloat(element.value);
		} else {
			// Для других элементов берем из textContent
			value = parseFloat(element.textContent);
		}
		// Форматируем значение
		let formattedValue = returnFormatted(value);
		if (element.tagName.toLowerCase() === 'input') {
			// Устанавливаем отформатированное значение в value
			element.value = formattedValue;
		} else {
			// Устанавливаем в textContent
			element.textContent = formattedValue;
		}
	});
}

/**
 * Форматирует числовое значение для отображения.
 * @param {number} value - Числовое значение для форматирования.
 * @returns {string} Отформатированная строка.
 */
function returnFormatted(value) {
	let formattedValue = '';
	if (!isNaN(value) && value !== 0) {
		// Форматируем с двумя знаками после запятой
		formattedValue = value.toFixed(2);
		// Если значение целое, убираем дробную часть
		if (value === parseInt(value)) {
			formattedValue = value.toFixed(0);
		}
	}
	return formattedValue;
}


/**
 * Инициализация скрипта после загрузки DOM.
 * Применяет слушатели событий и форматирует значения.
 */
document.addEventListener('DOMContentLoaded', function () {
	// Применяем слушатель для расчета сдачи
	applyCashBackListener();
	// Форматируем значения в элементах с классом formatValue
	FormatValues();
});