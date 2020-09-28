<?php

namespace Ctweb\SMSAuth\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\PhoneNumber;
use Bitrix\Main\UserTable;
use Bitrix\Main\Loader;

use Ctweb\SMSAuth\Manager;
use Ctweb\SMSAuth\Module;

Loc::loadMessages(__FILE__);

class Settings extends Controller
{
	public function configureActions()
	{
		return [
			'check' => [
				'prefilters' => []
			],
			'fix' => [
				'prefilters' => []
			],
		];
	}

	public function checkAction()
	{
		global $USER;
		if (!$USER->isAdmin()) {
			return [
				'Not allowed'
			];
		}
		$result = [];
		$users = $this->getUserIterator();
		$phoneField = $this->getPhoneField();
		$manager = new Manager();

		$autoFixCount = $manualFixCount = 0;

		while ($row = $users->fetch()) {
			$normalizedPhone = $manager->NormalizePhone($row[$phoneField]);

			if (!$this->isValidPhone($row[$phoneField])) {

				$info = [
					'phone' => $row[$phoneField],
					'normalized' => $normalizedPhone,
					'user_id' => $row['ID']
				];

				if ($this->isValidPhone($normalizedPhone)) {
					$info['action'] = 'auto';
					$autoFixCount++;
				} else {
					$info['action'] = 'manual';
					$manualFixCount++;
				}

				$result[] = $info;
			}
		}

		if ($manualFixCount === 0) {
			Option::set(Module::MODULE_ID, 'NO_PHONE_ERRORS', 1);
		}

		return [
			'result' => $result,
			'message' => Loc::getMessage('CWSA_CHECK_REPORT', [
				'TOTAL' => $autoFixCount+$manualFixCount,
				'AUTOFIX' => $autoFixCount,
				'MANUALFIX' => $manualFixCount,

			])
		];
	}



	public function fixAction()
	{
		global $DB, $USER;
		if (!$USER->isAdmin()) {
			return [
				'Not allowed'
			];
		}
		$users = $this->getUserIterator();
		$phoneField = $this->getPhoneField();
		$manager = new Manager();
		$u = new \CUser;

		$manualFixNeed = false;

		$DB->startTransaction();
		try {
			while ($row = $users->fetch()) {
				$normalizedPhone = $manager->NormalizePhone($row[$phoneField]);
				if (!$this->isValidPhone($row[$phoneField])) {

					if (!$this->isValidPhone($normalizedPhone)) {
						$manualFixNeed = true;
					} else {
						$res = $u->update($row['ID'], [
							$phoneField => $normalizedPhone
						]);

						if (!$res) {
							throw new \Exception($u->LAST_ERROR);
						}
					}
				}
			}

			$DB->commit();
			if (!$manualFixNeed) {
				Option::set(Module::MODULE_ID, 'NO_PHONE_ERRORS', 1);
			}
			return true;
		} catch (\Exception $e) {
			$DB->rollback();
			return [
				'result' => 'error',
				'message' => $e->getMessage()
			];
		}
	}

	private function getUserIterator()
	{
		$phoneField = $this->getPhoneField();

		return UserTable::getList([
			'filter' => [
				'!'.$phoneField => false,
			],
			'select' => ['ID', $phoneField]
		]);
	}

	private function getPhoneField()
	{
		static $field;

		if (!isset($field)) {
			$options = Module::getOptions();

			$field = $options["PHONE_FIELD"];
		}

		return $field;
	}

	private function isValidPhone($phone)
	{
		$phoneNumber = PhoneNumber\Parser::getInstance()->parse($phone);

		return $phoneNumber->isValid();
	}
}