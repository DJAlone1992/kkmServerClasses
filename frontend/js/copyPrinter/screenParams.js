let executed = false;
function screenParams() {
    if (!executed) {
        showOperationModal('Печать копии чека');

        const fiscalNumberInput = document.getElementById('fiscal-number');
        if (!fiscalNumberInput) {
            console.error('Поле fiscal-number не найдено');
            return;
        }
        const fiscalNumber = parseInt(fiscalNumberInput.textContent);
        if (isNaN(fiscalNumber) || fiscalNumber < 0) {
            showStatusError('Номер чека не указан');
            return;
        }

        executed = true;

        getCommandParams('GetDataCheck', {
			fiscalNumber: fiscalNumber,
			numberOfCopies: 1,
		});

    } else {
        const status = document.getElementById('copyPrintStatus');
        if (status) {
            status.textContent = 'Задание успешно отправлено. Данное окно можно закрыть';
        }
        setTimeout(window.close, 700)
    }
}