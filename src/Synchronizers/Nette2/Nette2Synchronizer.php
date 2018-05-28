<?php

/**
 *
 * Copyright (c) Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace ComposerSynchronizer\Synchronizers\Nette2;

use ComposerSynchronizer\Helpers;
use ComposerSynchronizer\Plugin;
use ComposerSynchronizer\Synchronizers\AbstractSynchronizer;
use ComposerSynchronizer\Synchronizers\SynchronizerInterface;


final class Nette2Synchronizer extends AbstractSynchronizer
{

	private const CONFIGURATION_FILE = 'composer-synchronizer.neon';
	private const CONFIGURATION_FILE_INDENTATION_CHARACTER = "\t";

	private const PATHS_PLACEHOLDERS = [
		'appDir' => 'app',
		'configDir' => 'app/config',
		'logDir' => 'log',
		'tempDir' => 'temp',
		'wwwDir' => 'www',
	];

	/**
	 * @var string
	 */
	private $configurationFile;


	public function init(): SynchronizerInterface
	{
		$this->configurationFile =
			$this->projectDirectory . '/' . self::PATHS_PLACEHOLDERS['configDir'] . '/' . self::CONFIGURATION_FILE;

		Helpers::copy(__DIR__ . '/resources/' . self::CONFIGURATION_FILE, $this->configurationFile);

		return $this;
	}


	public static function getAliases(): array
	{
		return ['nette'];
	}


	public static function getVersionedName(): string
	{
		return 'nette2';
	}


	protected function getConfigurationSections(): array
	{
		return [
			'includes' => [
				Plugin::INSTALL_EVENT_TYPE => function ($sectionContent) {
					$this->synchronizeIncludes($sectionContent);
				},
				Plugin::UNINSTALL_EVENT_TYPE => function ($sectionContent) {
					$this->desynchronizeIncludes($sectionContent);
				}
			]
		];
	}


	protected function getPathsPlaceholders(): array
	{
		return self::PATHS_PLACEHOLDERS;
	}


	/*************************** Synchronization methods ***************************/


	protected function synchronizeIncludes(array $includes): void
	{
		foreach($includes as $include) {
			$rowToAdd = $this->createRow($include);
			Helpers::appendToFile($this->configurationFile, $rowToAdd);
		}
	}


	protected function desynchronizeIncludes(array $includes): void
	{
		foreach($includes as $include) {
			$rowToRemove = $this->createRow($include);
			Helpers::removeFromFile($this->configurationFile, $rowToRemove);
		}
	}


	private function createRow(string $include): string
	{
		return self::CONFIGURATION_FILE_INDENTATION_CHARACTER . '- ' . $include . PHP_EOL;
	}

}
