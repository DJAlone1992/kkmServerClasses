/**
 * Модуль для работы с модальным окном печати копии чека.
 * Обрабатывает ввод номера документа, ввод количества копий, валидацию и отправку команд на сервер.
 */

/**
 * @var {Modal} printChequeCopyModalObj - Объект модального окна Bootstrap для печати копии чека
 */
let printChequeCopyModalObj = null;
// инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function () {
	// находим элемент модального окна
	const printChequeCopyModal = document.getElementById('printChequeCopyModal');
	if (!printChequeCopyModal) {
		console.error('Модальное окно printChequeCopyModal не найдено');
		return;
	}
	// инициализируем объект модального окна Bootstrap
	printChequeCopyModalObj = new bootstrap.Modal(printChequeCopyModal);

	// находим кнопку операции
	const operationButton = document.getElementById('GetDataCheck');
	if (!operationButton) {
		console.error('Кнопка GetDataCheck не найдена');
		return;
	}

	// находим поле номера документа
	const fiscalNumberInput = document.getElementById('fiscal-number');
	if (!fiscalNumberInput) {
		console.error('Поле fiscal-number не найдено');
		return;
	}
	// находим поле номера документа
	const numberOfCopiesInput = document.getElementById('number-of-copies');
	if (!numberOfCopiesInput) {
		console.error('Поле number-of-copies не найдено');
		return;
	}
	// обработчик перед скрытием модального окна
	printChequeCopyModal.addEventListener('hide.bs.modal', (event) => {
		// очищаем поля
		fiscalNumberInput.value = '';
		numberOfCopiesInput.value = '';

		// очищаем классы валидации
		fiscalNumberInput.classList.remove('is-invalid', 'is-valid');
		numberOfCopiesInput.classList.remove('is-invalid', 'is-valid');
	});

	// единый обработчик клика по кнопке операции
	operationButton.addEventListener('click', () => {
		// находим поле номера документа
		const fiscalNumberInput = document.getElementById('fiscal-number');
		if (!fiscalNumberInput) {
			console.error('Поле fiscal-number не найдено');
			return;
		}
		// находим поле номера документа
		const numberOfCopiesInput = document.getElementById('number-of-copies');
		if (!numberOfCopiesInput) {
			console.error('Поле number-of-copies не найдено');
			return;
		}

		let fiscalNumber = parseInt(fiscalNumberInput.value);
		if (isNaN(fiscalNumber) || fiscalNumber < 0) {
			fiscalNumber = 0;
		}
		if (fiscalNumber > 9999999999) {
			fiscalNumberInput.classList.add('is-invalid');
			return;
		}

		let numberOfCopies = parseInt(numberOfCopiesInput.value);
		if (isNaN(numberOfCopies) || numberOfCopies < 1) {
			numberOfCopies = 1;
		}
		if (numberOfCopies > 99) {
			numberOfCopiesInput.classList.add('is-invalid');
			return;
		}
		printChequeCopyModalObj.hide();
		// Отображаем модальное окно с названием операции
		showOperationModal('Печать копии последнего чека');
		// Отправляем запрос параметров команды на сервер
		getCommandParams('GetDataCheck', {
			fiscalNumber: fiscalNumber,
			numberOfCopies: numberOfCopies,
		});
	});
});
