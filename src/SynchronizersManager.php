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
use ComposerSynchronizer\Synchronizers\SynchronizerInterface;
use stdClass;


final class SynchronizersManager
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
	 * @var PackagesManager
	 */
	private $packagesManager;

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

		$this->packagesManager = new PackagesManager();

		$this->projectDirectory = $vendorDirectory .  '/..';
		$this->lockFilePath = $this->projectDirectory . '/' . self::LOCK_FILE_NAME;
	}


	public function processPackage(PackageInterface $package): void
	{
		$synchronizer = $this->getSynchronizer($this->configuration->{'project-type'});

		if ( ! $synchronizer)  {
			return;
		}

		$this->processedPackage = $package;

		$this->packagesManager
			->setComposerEventType($this->composerEventType)
			->setProjectType($synchronizer->getVersionedName())
			->setVendorDirectory($this->vendorDirectory);

		$packageConfiguration = $this->packagesManager->getPackageConfiguration($package);
		$isInstallEvent = $this->composerEventType === Plugin::INSTALL_EVENT_TYPE;

		if ( ! $packageConfiguration
			|| ! in_array($packageConfiguration->packageType, self::ALLOWED_COMPOSER_TYPES, true)
			|| $isInstallEvent && Helpers::fileContains($this->lockFilePath, $this->processedPackage->getPrettyName())
		) {
			return;
		}

		$packageInfo = '<info>' . $package->getName() . '</info>';
		$packageComment = '<comment>' . $package->getFullPrettyVersion() . '</comment>';
		$consoleLineActionName = $isInstallEvent ? 'Synchronizing' : 'Desynchronizing';
		Helpers::consoleMessage('%s %s (%s)', [$consoleLineActionName, $packageInfo, $packageComment]);

		$synchronizer
			->setComposerEventType($this->composerEventType)
			->setPackageConfiguration($packageConfiguration)
			->setProjectConfiguration($this->configuration)
			->setProjectDirectory($this->projectDirectory)
			->init()
			->synchronize();

		$isInstallEvent
			? Helpers::appendToFile($this->lockFilePath, $package->getName() . PHP_EOL)
			: Helpers::removeFromFile($this->lockFilePath, $package->getName() . PHP_EOL);
	}


	private function getSynchronizer(string $name): ?SynchronizerInterface
	{
		$selectedSynchronizer = null;

		/** @var SynchronizerInterface $synchronizer */
		foreach (self::SYNCHRONIZERS_REGISTER as $synchronizer) {
			if ($name === $synchronizer::getVersionedName() || in_array($name, $synchronizer::getAliases(), true)) {
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
