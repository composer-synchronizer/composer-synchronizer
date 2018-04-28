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

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;


final class Plugin implements PluginInterface, EventSubscriberInterface
{

	public const COMPOSER_SECTION_NAME = 'composer-synchronizer';

	public const INSTALL_EVENT_TYPE = 'install';
	public const UNINSTALL_EVENT_TYPE = 'uninstall';


	public function activate(Composer $composer, IOInterface $io): void
	{

	}


	public static function getSubscribedEvents(): array
	{
		return [
			PackageEvents::POST_PACKAGE_INSTALL => self::INSTALL_EVENT_TYPE,
			PackageEvents::PRE_PACKAGE_UNINSTALL => self::UNINSTALL_EVENT_TYPE
		];
	}


	public function install(PackageEvent $event): void
	{
		$this->runSynchronizersInitializer(self::INSTALL_EVENT_TYPE, $event);
	}


	public function uninstall(PackageEvent $event): void
	{
		$this->runSynchronizersInitializer(self::UNINSTALL_EVENT_TYPE, $event);
	}


	private function runSynchronizersInitializer(string $eventType, PackageEvent $event): void
	{
		Helpers::setIo($event->getIO());
		$composerExtraSection = $event->getComposer()->getPackage()->getExtra();
		$event->getComposer()->getRepositoryManager()->getLocalRepository();
		$synchronizerConfiguration = isset($composerExtraSection[self::COMPOSER_SECTION_NAME])
			? (object) $composerExtraSection[self::COMPOSER_SECTION_NAME]
			: null;

		if ( ! $synchronizerConfiguration) {
			return;

		} elseif ( ! isset($synchronizerConfiguration->{'project-type'})) {
			Helpers::consoleMessage('Composer synchronizer: project-type section is missing.');
			return;
		}

		$synchronizersMaster = new SynchronizersManager(
			$synchronizerConfiguration, $eventType, $event->getComposer()->getConfig()->get('vendor-dir')
		);

		/** @var InstallOperation|UninstallOperation $eventOperation */
		$eventOperation = $event->getOperation();
		$synchronizersMaster->processPackage($eventOperation->getPackage());
	}

}
