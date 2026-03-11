/**
 * Список индексов полей в ответе сервера при запросе статуса ККТ
 * @field {Boolean} short - Признак вывода в кратком статусе
 * @field {String} name - Название поля
 * @field {Function} process - Функция обработки значения поля
 */
const kktStatusFields = {
	InnOrganization: {
		short: true,
		name: 'ИНН организации',
	},
	NameOrganization: {
		short: true,
		name: 'Наименование организации',
	},
	TaxVariant: {
		short: true,
		name: 'Вид налогообложения',
		process: (value) => {
			let variants = value.split(',').map((item) => parseInt(item.trim()));
			let result = [];
			variants.forEach((variant) => {
				result.push(TaxVariantName(variant));
			});
			return result.join(', ');
		},
	},
	AddressSettle: {
		short: false,
		name: 'Юридический адрес',
	},
	PlaceSettle: {
		short: false,
		name: 'Адрес проведения расчетов',
	},
	SenderEmail: {
		short: false,
		name: 'E-mail организации',
	},
	EncryptionMode: {
		short: false,
		name: 'Режим шифрования',
	},
	OfflineMode: {
		short: false,
		name: 'Режим офлайн',
	},
	AutomaticMode: {
		short: false,
		name: 'Автоматический режим',
	},
	AutomaticNumber: {
		short: false,
		name: 'Номер автоматического режима',
	},
	InternetMode: {
		short: false,
		name: 'Режим интернета',
	},
	BSOMode: {
		short: false,
		name: 'Режим БСО',
	},
	ServiceMode: {
		short: false,
		name: 'Режим сервиса',
	},
	PrinterAutomatic: {
		short: false,
		name: 'Принтер автоматический',
	},
	SaleExcisableGoods: {
		short: false,
		name: 'Продажа подакцизных товаров',
	},
	SignOfGambling: {
		short: false,
		name: 'Признак азартной игры',
	},
	SignOfLottery: {
		short: false,
		name: 'Признак лотереи',
	},
	SaleMarking: {
		short: false,
		name: 'Продажа маркированных товаров',
	},
	SignPawnshop: {
		short: false,
		name: 'Признак ломбардов',
	},
	SignAssurance: {
		short: false,
		name: 'Признак гарантии',
	},
	SignOfAgent: {
		short: false,
		name: 'Признак агента',
	},
	UrlServerOfd: {
		short: false,
		name: 'Адрес сервера ОФД',
	},
	PortServerOfd: {
		short: false,
		name: 'Порт сервера ОФД',
	},
	NameOFD: {
		short: true,
		name: 'Название ОФД',
	},
	UrlOfd: {
		short: false,
		name: 'Сайт ОФД',
	},
	InnOfd: {
		short: false,
		name: 'ИНН ОФД',
	},
	OFD_Error: {
		short: true,
		name: 'Ошибка ОФД',
	},
	OFD_NumErrorDoc: {
		short: true,
		name: 'Числовой код ошибки ОФД',
	},
	OFD_DateErrorDoc: {
		short: true,
		name: 'Дата и время ошибки ОФД',
		process: (value) => moment(value).locale('ru').format('LLL'),
	},
	KktNumber: {
		short: false,
		name: 'Заводской номер ККТ',
	},
	FnNumber: {
		short: false,
		name: 'Заводской номер ФН',
	},
	RegNumber: {
		short: false,
		name: 'Регистрационный номер ККТ',
	},
	FN_IsFiscal: {
		short: false,
		name: 'Фискальный режим ФН',
	},
	FN_MemOverflowl: {
		short: false,
		name: 'Признак переполнения памяти ФН',
	},
	FN_DateStart: {
		short: false,
		name: 'Дата начала эксплуатации ФН',
		process: (value) => moment(value).locale('ru').format('LLL'),
	},
	FN_DateEnd: {
		short: false,
		name: 'Дата окончания эксплуатации ФН',
		process: (value) => moment(value).locale('ru').format('LLL'),
	},
	FFDVersion: {
		short: false,
		name: 'Версия ФФД',
	},
	FFDVersionFN: {
		short: false,
		name: 'Версия ФФД для ФН',
	},
	FFDVersionKKT: {
		short: false,
		name: 'Версия ФФД для ККТ',
	},
	OnOff: {
		short: false,
		name: 'Включен',
	},
	Active: {
		short: false,
		name: 'Активен',
	},
	SessionState: {
		short: true,
		name: 'Состояние смены',
		process: (value) => shiftStateName(value),
	},
	PaperOver: {
		short: true,
		name: 'Признак окончания бумаги',
	},
	BalanceCash: {
		short: true,
		name: 'Остаток наличных в кассе',
	},
	DateTimeKKT: {
		short: true,
		name: 'Дата и время ККТ',
		process: (value) => moment(value).locale('ru').format('LLL'),
	},
	Firmware_Version: {
		short: false,
		name: 'Версия прошивки',
	},
	Firmware_Status: {
		short: false,
		name: 'Статус прошивки',
	},
	LicenseExpirationDate: {
		short: false,
		name: 'Дата окончания лицензии',
		process: (value) => moment(value).locale('ru').format('LLL'),
	},
};
/**
 * Получение названия налоговой ставки
 * @param {Number} variant - номер налоговой ставки
 * @returns {String} - название налоговой ставки
 */
function TaxVariantName(variant) {
	let result = '';
	switch (variant) {
		case 0:
			result = 'Общая ОСН';
			break;
		case 1:
			result = 'Упрощенная УСН (Доход)';
			break;
		case 2:
			result = 'Упрощенная УСН (Доход минус Расход)';
			break;
		case 3:
			result = 'Единый налог на вмененный доход ЕНВД';
			break;
		case 4:
			result = 'Единый сельскохозяйственный налог ЕСН';
			break;
		case 5:
			result = 'Патентная система налогообложения';
			break;
	}
	return result;
}
/**
 * Получение названия состояния смены
 * @param {Number} value - номер состояния смены
 * @returns {String} - название состояния смены
 */
function shiftStateName(value) {
	switch (value) {
		case 1:
			return 'Смена закрыта';
		case 2:
			return 'Смена открыта';
		case 3:
			return 'Смена открыта более 24 часов';
		default:
			return value;
	}
}
