// Глобальная переменная для хранения модального окна предварительного просмотра чека перед печатью
let chequePrePrintModal = null;

/**
 * Инициализация скрипта после загрузки DOM.
 * Настраивает модальное окно, применяет слушатели событий и форматирует значения.
 */
document.addEventListener('DOMContentLoaded', function () {
	// Получаем элемент модального окна и инициализируем его с помощью Bootstrap
	const chequePrePrintElement = document.getElementById('chequePrePrint');
	if (!chequePrePrintElement) {
		console.error('Элемент с id "chequePrePrint" не найден.');
		return;
	}
	chequePrePrintModal = new bootstrap.Modal(chequePrePrintElement);

	// Применяем слушатель для кнопки печати чека
	applyPrintChequeListener();

	// Добавляем слушатель для кнопки запуска чека
	const runChequeButton = document.getElementById('runCheque');
	if (runChequeButton) {
		runChequeButton.addEventListener('click', runCheque);
	} else {
		console.error('Элемент с id "runCheque" не найден.');
	}
});

/**
 * Применяет слушатель события клика к кнопке печати чека.
 * Проверяет условия перед показом модального окна.
 */
function applyPrintChequeListener() {
	const printChequeButton = document.getElementById('printCheque');
	if (!printChequeButton) {
		console.error('Элемент с id "printCheque" не найден.');
		return;
	}
	printChequeButton.addEventListener('click', (event) => {
		// Предотвращаем стандартное поведение формы
		event.preventDefault();
		// Получаем элемент для отображения сдачи
		const change = document.getElementById('totalBackCash');
		if (!change) {
			console.error('Элемент с id "totalBackCash" не найден.');
			return;
		}
		// Проверяем, если сдача отрицательная (недостаточно денег)
		if (change.classList.contains('text-danger')) {
			showOperationModal('Печать чека');
			showStatusError(
				'Полученная сумма наличными не может быть меньше суммы наличных в чеке! Скорректируйте сумму полученных наличных',
			);
			return;
		}
		// Получаем сумму наличными из поля ввода
		const totalCashElement = document.getElementById('totalCash');
		if (!totalCashElement) {
			console.error('Элемент с id "totalCash" не найден.');
			return;
		}
		const totalCash = parseFloat(totalCashElement.value);
		// Получаем элемент для отображения суммы наличными в модальном окне
		const modalCashSumEl = document.getElementById('modalCashSum');
		if (!modalCashSumEl) {
			console.error('Элемент с id "modalCashSum" не найден.');
			return;
		}
		if (!isNaN(totalCash) && totalCash !== 0) {
			// Показываем элемент и устанавливаем отформатированное значение
			modalCashSumEl.style.visibility = 'visible';
			if (modalCashSumEl.firstChild) {
				modalCashSumEl.firstChild.textContent = returnFormatted(totalCash);
			}
		} else {
			// Скрываем элемент, если сумма равна нулю или невалидна
			modalCashSumEl.style.visibility = 'hidden';
		}
		// Получаем сумму электронными платежами
		const totalElectronElement = document.getElementById('totalElectron');
		if (!totalElectronElement) {
			console.error('Элемент с id "totalElectron" не найден.');
			return;
		}
		const totalElectron = parseFloat(totalElectronElement.value);
		// Получаем элемент для отображения суммы электронными платежами в модальном окне
		const modalElectronicSumEl = document.getElementById('modalElectronicSum');
		if (!modalElectronicSumEl) {
			console.error('Элемент с id "modalElectronicSum" не найден.');
			return;
		}
		if (!isNaN(totalElectron) && totalElectron !== 0) {
			// Показываем элемент и устанавливаем отформатированное значение
			modalElectronicSumEl.style.visibility = 'visible';
			if (modalElectronicSumEl.firstChild) {
				modalElectronicSumEl.firstChild.textContent = returnFormatted(totalElectron);
			}
		} else {
			// Скрываем элемент, если сумма равна нулю или невалидна
			modalElectronicSumEl.style.visibility = 'hidden';
		}
		// Показываем модальное окно предварительного просмотра
		if (chequePrePrintModal) {
			chequePrePrintModal.show();
		}
	});
}


/**
 * Запускает процесс печати чека.
 * Скрывает модальное окно, подготавливает данные и отправляет команду.
 */
function runCheque() {
	showOperationModal('Отправка чека на печать', 'Печать чека');
	if (chequePrePrintModal) {
		chequePrePrintModal.hide();
	}
	// Получаем подготовленный JSON из data-атрибута кнопки
	const runChequeButton = document.getElementById('runCheque');
	if (!runChequeButton) {
		console.error('Элемент с id "runCheque" не найден.');
		return;
	}
	let json;
	try {
		json = JSON.parse(runChequeButton.dataset.preparedJson);
	} catch (e) {
		console.error('Ошибка парсинга JSON из dataset.preparedJson:', e);
		return;
	}
	// Добавляем номер ККТ
	json.KktNumber = kktNumber;
	// Выполняем команду печати чека
	ExecuteCommand(json, printChequeCallback);
}
