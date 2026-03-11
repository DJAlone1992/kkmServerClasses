let chequePrePrintModal = null;

document.addEventListener('DOMContentLoaded', function () {
	chequePrePrintModal = new bootstrap.Modal(
		document.getElementById('chequePrePrint'),
	);
	applyPrintChequeListener();
	applyCashBackListener();
	FormatValues();
	document.getElementById('runCheque').addEventListener('click', runCheque);
});
function applyPrintChequeListener() {
	document.getElementById('printCheque').addEventListener('click', (event) => {
		event.preventDefault();
		const change = document.getElementById('totalBackCash');
		if (change.classList.contains('text-danger')) {
			showOperationModal('Печать чека');
			showStatusError(
				'Полученная сумма наличными не может быть меньше суммы наличных в чеке! Скорректируйте сумму полученных наличных',
			);
			return;
		}
		const totalCash = parseFloat(document.getElementById('totalCash').value);
		const modalCashSumEl = document.getElementById('modalCashSum');
		if (!isNaN(totalCash) && totalCash != 0) {
			modalCashSumEl.style.visibility = 'visible';
			modalCashSumEl.firstChild.textContent = returnFormatted(totalCash);
		} else {
			modalCashSumEl.style.visibility = 'hidden';
		}
		const totalElectron = parseFloat(
			document.getElementById('totalElectron').value,
		);
		const modalElectronicSumEl = document.getElementById('modalElectronicSum');
		if (!isNaN(totalElectron) && totalElectron != 0) {
			modalElectronicSumEl.style.visibility = 'visible';
			modalElectronicSumEl.firstChild.textContent =
				returnFormatted(totalElectron);
		} else {
			modalElectronicSumEl.style.visibility = 'hidden';
		}
		chequePrePrintModal.show();
	});
}
function applyCashBackListener() {
	const totalReceivedCash = document.getElementById('totalReceivedCash');
	totalReceivedCash.addEventListener('input', function () {
		const totalReceivedCashValue = parseFloat(totalReceivedCash.value);
		const totalAmount = parseFloat(document.getElementById('totalCash').value);
		const change = document.getElementById('totalBackCash');
		if (isNaN(totalReceivedCashValue)) {
			change.value = '0.00';
		} else {
			const changeValue = totalReceivedCashValue - totalAmount;
			change.value = changeValue.toFixed(2);
			if (changeValue < 0) {
				change.classList.add('text-danger');
				change.classList.remove('text-success');
			} else {
				change.classList.add('text-success');
				change.classList.remove('text-danger');
			}
		}
	});
}

function runCheque() {
	showOperationModal('Отправка чека на печать', 'Печать чека');
	chequePrePrintModal.hide();
	let json = JSON.parse(
		document.getElementById('runCheque').dataset.preparedJson,
	);
	json.KktNumber = kktNumber;
	ExecuteCommand(json, printChequeCallback);
}

function FormatValues() {
	const formatValueElements = document.querySelectorAll('.formatValue');
	formatValueElements.forEach(function (element) {
		let value;
		if (element.tagName.toLowerCase() === 'input') {
			value = parseFloat(element.value);
		} else {
			value = parseFloat(element.textContent);
		}
		let formattedValue = returnFormatted(value);
		if (element.tagName.toLowerCase() === 'input') {
			element.value = formattedValue;
		} else {
			element.textContent = formattedValue;
		}
	});
}

function returnFormatted(value) {
	let formattedValue = '';
	if (!isNaN(value) && value !== 0) {
		formattedValue = value.toFixed(2);
		if (value == parseInt(value)) {
			formattedValue = value.toFixed(0);
		}
	}
	return formattedValue;
}
function printChequeCallback(response) {
	const callbackUrl = document.getElementById('runCheque')?.dataset?.callbackUrl??'';
	ExecuteSuccess(response);
	if (response.Status !== 0) {
		return;
	}
	showOperationModal(
		'Отправляем запрос подтверждения',
		'Отправка подтверждения в верхнее приложение',
	);
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
