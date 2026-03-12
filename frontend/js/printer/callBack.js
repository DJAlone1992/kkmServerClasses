
/**
 * Callback функция для обработки ответа от команды печати чека.
 * @param {object} response - Ответ от сервера.
 */
function printChequeCallback(response) {
	// Выполняем успешную обработку ответа
	ExecuteSuccess(response);
	if (response.Status !== 0) {
		// Если статус не успешный, выходим
		return;
	}
	setTimeout(() => {
		sendCallback(response);
	}, 1500);
}

function sendCallback(response) {
	// Получаем URL для callback из data-атрибута
	const runChequeButton = document.getElementById('runCheque');
	const callbackUrl = runChequeButton?.dataset?.callbackUrl ?? '';
	// Показываем модальное окно для отправки подтверждения
	showOperationModal(
		'Отправляем запрос подтверждения',
		'Отправка подтверждения в верхнее приложение',
	);
	// Отправляем запрос подтверждения
	myFetch(
		'/' + callbackUrl + '?data=' + JSON.stringify(response) + '&action=confirm',
		function (response) {
			if (response.error) {
				showStatusError(response.errorText);
			} else {
				showStatusSuccess(response.successText);
				setTimeout(window.close, 1500);
			}
		},
		false,
	);
}
