/**
 * Открытие смены
 * @returns void
 */
function openShift() {
	showOperationModal('Открытие смены');
	getCommandParams('openShift');
}
/**
 * X-отчет
 * @returns void
 */
function XReport() {
	showOperationModal('X-отчет');
	getCommandParams('XReport');
}
/**
 * Закрытие смены (Z-отчет)
 * @returns void
 */
function closeShift() {
	showOperationModal('Закрытие смены');
	getCommandParams('closeShift');
}
/**
 * Статус ККТ
 * @returns void
 */
function KKTStatus() {
	showOperationModal('Статус ККТ');
	getCommandParams('KKTStatus', {}, printKKTStatus);
}
/**
 * Полный статус ККТ
 * @returns void
 */
function KKTFullStatus() {
	showOperationModal('Полный статус ККТ');
	getCommandParams('KKTStatus', {}, printKKTFullStatus);
}
/**
 * Вывод статуса ККТ
 * @param {Object} resultJson - ответ сервера
 * @returns void
 */
function printKKTStatus(resultJson) {
	showKKTStatus(resultJson.Info, true);
}
/**
 * Вывод полного статуса ККТ
 * @param {Object} resultJson - ответ сервера
 * @returns void
 */
function printKKTFullStatus(resultJson) {
	showKKTStatus(resultJson.Info);
}
/**
 * Вывод статуса ККТ в модальном окне
 * @param {Object} resultJson - ответ сервера
 * @param {Boolean} ifShort - выводить только основные поля
 * @returns void
 */
function showKKTStatus(resultJson, ifShort = false) {
	let lines = [];
	let caption = '';
	let processFunc = (value) => value;
	for (let key in kktStatusFields) {
		//Если параметр не должен выводиться в кратком спике, и выбрано отображение краткого списка - то выходим
		if (ifShort && !kktStatusFields[key].short) {
			continue;
		}
		//Если нет функции обработки, то генерируем простую функцию
		processFunc = kktStatusFields[key].process || ((value) => value);
		//Получаем название поля
		caption = kktStatusFields[key].name;
		//Если есть значение в ответе сервера
		if (resultJson[key] !== undefined) {
			if (resultJson[key] === null) {
				continue;
			}
			if (resultJson[key] === '') {
				continue;
			}
			//Если значение - логическое, то меняем его на "Да" или "Нет"
			if (typeof resultJson[key] === 'boolean') {
				resultJson[key] = resultJson[key] ? 'Да' : 'Нет';
			}
			// Получаем значение через функцию обработки
			resultJson[key] = processFunc(resultJson[key]);
			//Добавляем в список
			lines.push(
				`<li class="list-group-item list-group-item-info"><strong>${caption}:</strong> ${resultJson[key]}</li>`,
			);
		}
	}
	//Если есть данные
	if (lines.length) {
		//Собираем список
		const content = `<ul class="list-group list-group-flush">${lines.join('')}</ul>`;
		showStatusInfo(content);
	} else {
		//Выводим ошибку
		showStatusError('Нет данных');
	}
}
/**
 * Инициализация
 */
document.addEventListener('DOMContentLoaded', function () {
	document.getElementById('openShift').addEventListener('click', openShift);
	document.getElementById('closeShift').addEventListener('click', closeShift);
	document.getElementById('XReport').addEventListener('click', XReport);
	document.getElementById('KKTStatus').addEventListener('click', KKTStatus);
	document
		.getElementById('KKTFullStatus')
		.addEventListener('click', KKTFullStatus);
});
