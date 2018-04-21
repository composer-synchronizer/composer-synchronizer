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

namespace ComposerSynchronizer;

use Composer\Package\PackageInterface;
use ComposerSynchronizer\Helpers;
use ComposerSynchronizer\Synchronizers;
use ComposerSynchronizer\Synchronizers\SynchronizerConfigurationInterface;
use ComposerSynchronizer\Synchronizers\SynchronizerInterface;
use stdClass;


final class SynchronizersMaster
{

	private const ALLOWED_COMPOSER_TYPES = ['composer-plugin', 'library', null];

	private const LOCK_FILE_NAME = 'composer-synchronizator.lock';

	private const SYNCHRONIZERS_REGISTER = [
		Synchronizers\Nette2\Nette2Synchronizer::class
	];

	/**
	 * @var string
	 */
	private $composerEventType;

	/**
	 * @var stdClass
	 */
	private $configuration;

	/**
	 * @var string
	 */
	private $vendorDirectory;

	/**
	 * @var string
	 */
	private $lockFilePath;

	/**
	 * @var string
	 */
	private $projectDirectory;

	/**
	 * @var PackageInterface
	 */
	private $processedPackage;


	public function __construct(
		stdClass $configuration,
		string $eventType,
		string $vendorDirectory
	) {
		$this->configuration = $configuration;
		$this->composerEventType = $eventType;
		$this->vendorDirectory = $vendorDirectory;

		$this->projectDirectory = $vendorDirectory .  '/..';
		$this->lockFilePath = $this->projectDirectory . '/' . self::LOCK_FILE_NAME;
	}


	public function processPackage(PackageInterface $package): void
	{
		$this->processedPackage = $package;
		$packageName = $package->getName();
		$packageDirectory = $this->vendorDirectory . '/' . $packageName;
		$packageConfiguration = $this->getPackageComposerConfiguration($packageDirectory);
		if ( ! $packageConfiguration) {
			return;
		}

		$packageType = isset($packageConfiguration->type) ? $packageConfiguration->type : null;
		$packageSynchronizerConfiguration = isset($packageConfiguration->extra->{Plugin::COMPOSER_SECTION_NAME})
			? $packageConfiguration->extra->{Plugin::COMPOSER_SECTION_NAME}
			: null;
		$isInstallEvent = $this->composerEventType === Plugin::INSTALL_EVENT_TYPE;

		/* TODO try to load missing package configuration from github package list
		 if ( ! $packageSynchronizerConfiguration) {
		}*/

		if ( ! $packageSynchronizerConfiguration
			|| ! in_array($packageType, self::ALLOWED_COMPOSER_TYPES, true)
			|| $isInstallEvent && Helpers::fileContains($this->lockFilePath, $packageConfiguration->name)
		) {
			return;
		}

		$projectType = $this->configuration->{'project-type'};
		$synchronizer = $this->getSynchronizer($projectType);

		if ( ! $synchronizer || ! isset($packageSynchronizerConfiguration->{$projectType}))  {
			return;
		}

		$packageInfo = '<info>' . $packageName . '</info>';
		$packageComment = '<comment>' . $package->getFullPrettyVersion() . '</comment>';
		$consoleLineActionName = $isInstallEvent ? 'Synchronizing' : 'Desynchronizing';
		Helpers::consoleMessage('%s %s (%s)', [$consoleLineActionName, $packageInfo, $packageComment]);

		$synchronizer
			->setComposerEventType($this->composerEventType)
			->setPackageConfiguration($packageSynchronizerConfiguration->{$projectType})
			->setPackageDirectory($packageDirectory)
			->setPackageName($packageConfiguration->name)
			->setProjectConfiguration($this->configuration)
			->setProjectDirectory($this->projectDirectory)
			->init()
			->synchronize();

		$isInstallEvent
			? Helpers::appendToFile($this->lockFilePath, $packageName . PHP_EOL)
			: Helpers::removeFromFile($this->lockFilePath, $packageName . PHP_EOL);
	}


	private function getPackageComposerConfiguration(string $packageDirectory): ?stdClass
	{
		$composerJsonFile = $packageDirectory . '/composer.json';

		if ( ! is_file($composerJsonFile)) {
			Helpers::consoleMessage('Composer synchronizer: file %s not found.', [$composerJsonFile]);
			return null;
		}

		$data = json_decode(Helpers::loadFileContent($composerJsonFile));
		if ( ! $data instanceof stdClass) {
			Helpers::consoleMessage('Composer synchronizer: invalid %s.', [$composerJsonFile]);
			return null;
		}

		return $data;
	}


	private function getSynchronizer(string $name): ?SynchronizerConfigurationInterface
	{
		$selectedSynchronizer = null;

		/** @var SynchronizerInterface $synchronizer */
		foreach (self::SYNCHRONIZERS_REGISTER as $synchronizer) {
			if ($name === $synchronizer::getVersionedName() || in_array($name, $synchronizer::getAliases())) {
				$selectedSynchronizer = $synchronizer;
				break;
			}
		}

		if ( ! $selectedSynchronizer) {
			Helpers::consoleMessage(
				'Composer synchronizer: unknown project type <info>%s</info> for <info>%s</info>. Skipping.',
				[$name, $this->processedPackage->getName()]
			);
			return null;
		}

		return new $selectedSynchronizer();
	}

}
