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

namespace ComposerSynchronizer\Synchronizers\Yii2;

use ComposerSynchronizer\Helpers;
use ComposerSynchronizer\Plugin;
use ComposerSynchronizer\Synchronizers\AbstractSynchronizer;
use ComposerSynchronizer\Synchronizers\SynchronizerInterface;
use stdClass;


class Yii2Synchronizer extends AbstractSynchronizer
{

	private const CONFIGURATION_FILE = 'composer-synchronizer.php';

	private const PATHS_PLACEHOLDERS = [
		'commandsDir' => 'commands',
		'configDir' => 'config',
		'webDir' => 'web'
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
		return ['yii'];
	}


	public static function getVersionedName(): string
	{
		return 'yii2';
	}


	protected function getConfigurationSections(): array
	{
		return [
			'configs' => [
				Plugin::INSTALL_EVENT_TYPE => function ($sectionContent) {
					$this->synchronizeConfigs($sectionContent);
				},
				Plugin::UNINSTALL_EVENT_TYPE => function ($sectionContent) {
					$this->desynchronizeConfigs($sectionContent);
				}
			]
		];
	}


	protected function getPathsPlaceholders(): array
	{
		return self::PATHS_PLACEHOLDERS;
	}


	/*************************** Synchronization methods ***************************/


	protected function synchronizeConfigs(stdClass $configFiles): void
	{
		foreach($configFiles as $variableName => $path) {
			$rowToAdd = $this->createRow($variableName, $path);
			Helpers::appendToFile($this->configurationFile, $rowToAdd);
		}
	}


	protected function desynchronizeConfigs(stdClass $configFiles): void
	{
		foreach($configFiles as $variableName => $path) {
			$rowToRemove = $this->createRow($variableName, $path);
			Helpers::removeFromFile($this->configurationFile, $rowToRemove);
		}
	}


	private function createRow(string $variableName, string $path): string
	{
		return '$' . $variableName . ' = require_once __DIR__ . \'/' . ltrim($path, '/') . ';' . PHP_EOL;
	}

}
