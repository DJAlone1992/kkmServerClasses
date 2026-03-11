<?php

namespace Djalone\KkmServerClasses;

use Djalone\KkmServerClasses\Cheque\Enums\ChequeType;
use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
use Djalone\KkmServerClasses\Cheque\Items\Image;
use Djalone\KkmServerClasses\Cheque\Items\Item;
use Djalone\KkmServerClasses\Cheque\Items\Position;
use Djalone\KkmServerClasses\Cheque\Items\Text;
use Djalone\KkmServerClasses\Services\Helper;
/**
 * Команда печати чека.
 */
class Cheque extends Command
{
	/**
	 * Список текстовых полей
	 * @var array<Text> $texts
	 */
	private array $texts = [];
	/**
	 * Список позиций
	 * @var array<Position> $positions
	 */
	private array $positions = [];
	/**
	 * Счетчик позиций чека
	 * @var int $itemsCounter
	 */
	private int $itemsCounter = 0;
	/**
	 * @var int $cash Сумма наличных
	 */
	private int $cash = 0;
	/**
	 * @var int $electronicPayment Сумма электронного платежа
	 */
	private int $electronicPayment = 0;
	/**
	 * @var int $advancePayment Сумма предоплаты
	 */
	private int $advancePayment = 0;
	/**
	 * @var int $credit Сумма оплаты с кредита
	 */
	private int $credit = 0;
	/**
	 * @var int $cashProvision
	 */
	private int $cashProvision = 0;
	/**
	 * @var int $chequeType Тип чека
	 */
	private int $chequeType = ChequeType::INCOME;
	/**
	 * @var bool $isFiscal
	 */
	private bool $isFiscal = false;
	/**
	 * @var string $clientAddress
	 */
	private string $clientAddress = '';
	/**
	 * @var string $clientInfo
	 */
	private string $clientInfo = '';
	/**
	 * @var string $clientINN
	 */
	private string $clientINN = '';
	/**
	 * @var string $RRNCode
	 */
	private string $RRNCode = '';
	/**
	 * @var string $AuthorizationCode
	 */
	private string $AuthorizationCode = '';

	/**
	 * Конструктор чека.
	 *
	 * @param string $cashierName  Имя кассира (необязательно).
	 * @param string $cashierVatin   ИНН кассира (необязательно).
	 * @param string $kktNumber    Номер ККТ (необязательно).
	 * @param string $idCommand    Идентификатор команды (необязательно).
	 */
	public function __construct(
		string $cashierName = '',
		string $cashierVatin = '',
		string $kktNumber = '',
		string $idCommand = ''
	) {
		parent::__construct($cashierName, $cashierVatin, $kktNumber, $idCommand);
		$this->command = 'RegisterCheck';
	}

	/**
	 * Получить общую сумму наличных.
	 *
	 * @return int
	 */
	public function getCash(): int
	{
		return $this->cash;
	}

	/**
	 * Получить сумму электронного платежа.
	 *
	 * @return int
	 */
	public function getElectronicPayment(): int
	{
		return $this->electronicPayment;
	}

	/**
	 * Получить сумму предоплаты.
	 *
	 * @return int
	 */
	public function getAdvancePayment(): int
	{
		return $this->advancePayment;
	}

	/**
	 * Получить сумму оплаты с кредита.
	 *
	 * @return int
	 */
	public function getCredit(): int
	{
		return $this->credit;
	}

	/**
	 * Получить сумму обеспечения наличными.
	 *
	 * @return int
	 */
	public function getCashProvision(): int
	{
		return $this->cashProvision;
	}

	/**
	 * Получить все элементы чека (тексты и позиции).
	 *
	 * @return array<Item>
	 */
	public function getItems(): array
	{
		return array_merge($this->texts, $this->positions);
	}

	/**
	 * Возвращает enum типа чека.
	 *
	 * @return int
	 */
	public function getChequeType(): int
	{
		return $this->chequeType;
	}

	/**
	 * Determine if the cheque is fiscal.
	 *
	 * @return bool
	 */
	public function getIsFiscal(): bool
	{
		return $this->isFiscal;
	}

	/**
	 * @return string Client address or contact info.
	 */
	public function getClientAddress(): string
	{
		return $this->clientAddress;
	}

	/**
	 * @return string Additional client information.
	 */
	public function getClientInfo(): string
	{
		return $this->clientInfo;
	}

	/**
	 * @return string Client INN (tax ID).
	 */
	public function getClientINN(): string
	{
		return $this->clientINN;
	}

	/**
	 * @return string Код транзакции RRN.
	 */
	public function getRRNCode(): string
	{
		return $this->RRNCode;
	}

	/**
	 * @return string Авторизационный код транзакции.
	 */
	public function getAuthorizationCode(): string
	{
		return $this->AuthorizationCode;
	}

	/**
	 * Добавить элемент (позицию или текст) в чек.
	 *
	 * @param Position|Text|Image $item Добавляемый элемент.
	 *
	 * @return static Текущий объект для цепочки вызовов.
	 */
	public function addItem(Item $item)
	{
		if ($item instanceof Position) {
			$this->addPosition($item);
		} elseif ($item instanceof Text) {
			$this->addText($item);
		}
		return $this;
	}
	/**
	 * Добавить текстовый элемент в чек.
	 *
	 * @param Text $text Текстовый элемент.
	 * @return static Текущий объект для цепочки.
	 */
	public function addText(Text $text)
	{
		$this->texts[$this->itemsCounter] = $text;
		$this->itemsCounter++;
		return $this;
	}

	/**
	 * Добавить позицию товара и скорректировать суммы в зависимости от типа оплаты.
	 *
	 * @param Position $position Позиция товара.
	 * @return static Текущий объект для цепочки.
	 */
	public function addPosition(Position $position)
	{
		$this->positions[$this->itemsCounter] = $position;
		$this->itemsCounter++;
		switch ($position->getPaymentType()) {
			case PaymentTypes::CashProvision:
				$this->cashProvision += $position->getAmount();
				break;
			case PaymentTypes::Credit:
				$this->credit += $position->getAmount();
				break;
			case PaymentTypes::Electronic:
				$this->electronicPayment += $position->getAmount();
				break;
			default:
				$this->cash += $position->getAmount();
				break;
		}
		return $this;
	}

	/**
	 * Установить массив позиций, заменяя существующие элементы.
	 *
	 * @param array<Position> $positions
	 * @return static Текущий объект для цепочки.
	 */
	public function setPositions(array $positions)
	{
		if (count($positions) < 1) {
			return $this;
		}
		$this->positions = $positions;
		$this->updateCounter($positions);
		return $this;
	}

	/**
	 * Заменить список текстовых элементов.
	 *
	 * @param array<Text> $texts
	 * @return static Текущий объект для цепочки.
	 */
	public function setTexts(array $texts)
	{
		if (count($texts) < 1) {
			return $this;
		}
		$this->texts = $texts;
		$this->updateCounter($texts);
		return $this;
	}
	/**
	 * Обновить счетчик позиций.
	 * @param array $array Массив элементов.
	 */
	private function updateCounter(array $array): void
	{
		$keys = array_keys($array);
		$maxKey = max($keys ?? []) ?? 0;
		$this->itemsCounter = max($this->itemsCounter ?? 0, $maxKey);
	}

	/**
	 * Получить массив позиции товаров.
	 *
	 * @return array<Position>
	 */
	public function getPositions(): array
	{
		return $this->positions;
	}

	/**
	 * Получить массив текстовых элементов.
	 *
	 * @return array<Text>
	 */
	public function getTexts(): array
	{
		return $this->texts;
	}

	/**
	 * Установить общую сумму наличных.
	 *
	 * @param int $cash
	 * @return static Текущий объект для цепочки.
	 */
	public function setCash(int $cash)
	{
		$this->cash = $cash;
		return $this;
	}
	/**
	 * Установить сумму электронной оплаты.
	 *
	 * @param int $electronicPayment
	 * @return static Текущий объект для цепочки.
	 */
	public function setElectronicPayment(int $electronicPayment)
	{
		$this->electronicPayment = $electronicPayment;
		return $this;
	}
	/**
	 * Установить сумму аванса.
	 *
	 * @param int $advancePayment
	 * @return static Текущий объект для цепочки.
	 */
	public function setAdvancePayment(int $advancePayment)
	{
		$this->advancePayment = $advancePayment;
		return $this;
	}
	/**
	 * Установить сумму кредита.
	 *
	 * @param int $credit
	 * @return static Текущий объект для цепочки.
	 */
	public function setCredit(int $credit)
	{
		$this->credit = $credit;
		return $this;
	}
	/**
	 * Установить сумму залога наличных.
	 *
	 * @param int $cashProvision
	 * @return static Текущий объект для цепочки.
	 */
	public function setCashProvision(int $cashProvision)
	{
		$this->cashProvision = $cashProvision;
		return $this;
	}
	/**
	 * Установить тип чека.
	 *
	 * @param mixed $chequeType
	 * @return static Текущий объект для цепочки.
	 * @param \Djalone\KkmServerClasses\Cheque\Enums\ChequeType::* $chequeType
	 */
	public function setChequeType($chequeType)
	{
		$this->chequeType = $chequeType;
		return $this;
	}
	/**
	 * Пометить чек как фискальный или нет.
	 *
	 * @param bool $isFiscal
	 * @return static Текущий объект для цепочки.
	 */
	public function setIsFiscal(bool $isFiscal)
	{
		$this->isFiscal = $isFiscal;
		return $this;
	}
	/**
	 * Установить адрес клиента.
	 *
	 * @param string $clientAddress
	 * @return static Текущий объект для цепочки.
	 */
	public function setClientAddress(string $clientAddress)
	{
		$this->clientAddress = $clientAddress;
		return $this;
	}
	/**
	 * Установить строку с информацией о клиенте.
	 *
	 * @param string $clientInfo
	 * @return static Текущий объект для цепочки.
	 */
	public function setClientInfo(string $clientInfo)
	{
		$this->clientInfo = $clientInfo;
		return $this;
	}
	/**
	 * Установить ИНН клиента.
	 *
	 * @param string $clientINN
	 * @return static Текущий объект для цепочки.
	 */
	public function setClientINN(string $clientINN)
	{
		$this->clientINN = $clientINN;
		return $this;
	}
	/**
	 * Установить код RRN.
	 *
	 * @param string $RRNCode
	 * @return static Текущий объект для цепочки.
	 */
	public function setRRNCode(string $RRNCode)
	{
		$this->RRNCode = $RRNCode;
		return $this;
	}
	/**
	 * Установить авторизационный код.
	 *
	 * @param string $AuthorizationCode
	 * @return static Текущий объект для цепочки.
	 */
	public function setAuthorizationCode(string $AuthorizationCode)
	{
		$this->AuthorizationCode = $AuthorizationCode;
		return $this;
	}
	/**
	 * @return array<string, bool|string|mixed[][]|int>
	 */
	public function toArray(): array
	{
		return [
			'ClientAddress' => $this->clientAddress,
			'ClientInfo' => $this->clientInfo,
			'ClientINN' => $this->clientINN,
			'RRNCode' => $this->RRNCode,
			'AuthorizationCode' => $this->AuthorizationCode,
			'IsFiscalCheck' => $this->isFiscal,
			'TypeCheck' => $this->chequeType,
			'CheckStrings' => array_map(
				fn(Item $item) => $item->toArray(),
				$this->getItems()
			),
			'Cash' => Helper::toFloat($this->cash, 5),
			'ElectronicPayment' => Helper::toFloat($this->electronicPayment, 5),
			'AdvancePayment' => Helper::toFloat($this->advancePayment, 5),
			'Credit' => Helper::toFloat($this->credit, 5),
			'CashProvision' => Helper::toFloat($this->cashProvision, 5),
			'NumberCopies' => 0,
			'InternetMode' => false,
			'PrintSlipAfterCheck' => false,
			'PrintSlipForCashier' => true,
		];
	}

	/**
	 * Проверка валидности чека.
	 *
	 * @return bool True если чек валиден, иначе false.
	 */
	public function isValid(): bool
	{
		$error = !parent::isValid();

		if (strlen($this->clientAddress) < 3) {
			$error = true;
			$this->errors[] =
				'Контактная информация клиента не может быть короче 3 символов';
		}

		if (strlen($this->clientInfo) < 3) {
			$error = true;
			$this->errors[] =
				'Информация о клиенте не может быть короче 3 символов';
		}

		if (
			!preg_match(
				'/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/',
				$this->clientAddress
			) &&
			!preg_match(
				'/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
				$this->clientAddress
			)
		) {
			$error = true;
			$this->errors[] =
				'Контактная информация не является корректным номером телефона или E-mail адресом';
		}

		if (count($this->getItems()) == 0) {
			$error = true;
			$this->errors[] = 'Чек не содержит ни одной позиции';
		}

		return !$error;
	}
}
