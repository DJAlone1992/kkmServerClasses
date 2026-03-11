
/**
 * Callback функция для обработки ответа от команды печати чека.
 * @param {object} response - Ответ от сервера.
 */
function printChequeCallback(response) {
	// Получаем URL для callback из data-атрибута
	const runChequeButton = document.getElementById('runCheque');
	const callbackUrl = runChequeButton?.dataset?.callbackUrl ?? '';
	// Выполняем успешную обработку ответа
	ExecuteSuccess(response);
	if (response.Status !== 0) {
		// Если статус не успешный, выходим
		return;
	}
	// Показываем модальное окно для отправки подтверждения
	showOperationModal(
		'Отправляем запрос подтверждения',
		'Отправка подтверждения в верхнее приложение',
	);
	// Отправляем запрос подтверждения
	myFetch(
		callbackUrl + '?data=' + JSON.stringify(response) + '&action=confirm',
		function (response) {
			if (response.error) {
				showStatusError(response.errorText);
			} else {
				showStatusSuccess(response.successText);
			}
		},
		false,
	);
}
