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

namespace ComposerSynchronizer\Synchronizers\CakePhp3;

use ComposerSynchronizer\Helpers;
use ComposerSynchronizer\Plugin;
use ComposerSynchronizer\Synchronizers\AbstractSynchronizer;
use ComposerSynchronizer\Synchronizers\SynchronizerInterface;


class CakePhp3Synchronizer extends AbstractSynchronizer
{

	private const CONFIGURATION_FILE = 'composer-synchronizer.php';

	private const PATHS_PLACEHOLDERS = [
		'commandsDir' => 'commands',
		'configDir' => 'config',
		'webrootDir' => 'webroot'
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
		return ['cakePhp'];
	}


	public static function getVersionedName(): string
	{
		return 'cakePhp3';
	}


	protected function getPathsPlaceholders(): array
	{
		return self::PATHS_PLACEHOLDERS;
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


	/*************************** Synchronization methods ***************************/


	protected function synchronizeConfigs(array $configFiles): void
	{
		foreach($configFiles as $variableName => $path) {
			$rowToAdd = $this->createRow($path);
			Helpers::appendToFile($this->configurationFile, $rowToAdd);
		}
	}


	protected function desynchronizeConfigs(array $configFiles): void
	{
		foreach($configFiles as $variableName => $path) {
			$rowToRemove = $this->createRow($path);
			Helpers::removeFromFile($this->configurationFile, $rowToRemove);
		}
	}


	private function createRow(string $path): string
	{
		return 'require_once __DIR__ . \'/' . ltrim($path, '/') . ';' . PHP_EOL;
	}

}
