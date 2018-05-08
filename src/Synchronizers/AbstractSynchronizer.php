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

namespace ComposerSynchronizer\Synchronizers;

use ComposerSynchronizer\Helpers;
use ComposerSynchronizer\Plugin;
use stdClass;


abstract class AbstractSynchronizer implements SynchronizerInterface
{

	private const GITIGNORE_FILE_NAME = '.gitignore';
	private const GITIGNORE_HASHTAG_CHAIN = '########';
	private const GITIGNORE_OPEN_TAG = self::GITIGNORE_HASHTAG_CHAIN . '<';
	private const GITIGNORE_CLOSE_TAG = self::GITIGNORE_HASHTAG_CHAIN . '>';

	private const PATH_PLACEHOLDER_DELIMITER = '%';

	/**
	 * @var string
	 */
	private $composerEventType;

	/**
	 * @var stdClass
	 */
	public $packageConfiguration;

	/**
	 * @var string
	 */
	public $packageDirectory;

	/**
	 * @var string
	 */
	public $packageName;

	/**
	 * @var stdClass
	 */
	public $projectConfiguration;

	/**
	 * @var string
	 */
	public $projectDirectory;

	/**
	 * @var array
	 */
	private $pathsPlaceholders;


	abstract protected function getConfigurationSections(): array;


	abstract protected function getPathsPlaceholders(): array;


	public final function synchronize(): void
	{
		$configurationSections = array_merge(
			$this->getDefaultConfigurationSections(),
			$this->getConfigurationSections()
		);

		foreach($this->packageConfiguration as $packageConfigurationSectionName => $values) {
			if (isset($configurationSections[$packageConfigurationSectionName][$this->composerEventType])) {
				$configurationSections[$packageConfigurationSectionName][$this->composerEventType]($values);
			}
		}
	}


	private function getDefaultConfigurationSections(): array
	{
		return [
			'gitignore' => [
				Plugin::INSTALL_EVENT_TYPE => function ($sectionContent) {
					$this->synchronizeGitignore($sectionContent);
				},
				Plugin::UNINSTALL_EVENT_TYPE => function ($sectionContent) {
					$this->desynchronizeGitignore($sectionContent);
				}
			],
			'resources' => [
				Plugin::INSTALL_EVENT_TYPE => function ($sectionContent) {
					$this->synchronizeResources($sectionContent);
				}
			]
		];
	}


	public final function setComposerEventType(string $type): SynchronizerInterface
	{
		$this->composerEventType = $type;
		return $this;
	}


	public final function setPackageConfiguration(stdClass $configuration): SynchronizerInterface
	{
		$this->packageConfiguration = $configuration->synchronizerConfiguration;
		$this->packageDirectory = $configuration->synchronizerConfigurationDirectory;
		$this->packageName = $configuration->packageName;

		return $this;
	}


	public final function setProjectConfiguration(stdClass $configuration): SynchronizerInterface
	{
		$this->projectConfiguration = $configuration;
		return $this;
	}


	public final function setProjectDirectory(string $directory): SynchronizerInterface
	{
		$this->projectDirectory = $directory;
		return $this;
	}


	private function replacePathPlaceholders(string $content): string
	{
		if ( ! $this->pathsPlaceholders) {
			$pathsPlaceholders = $this->getPathsPlaceholders();
			$projectPathsPlaceholdersReplacement = Helpers::getProperty(
				$this->projectConfiguration, 'paths-placeholders'
			);

			if ($projectPathsPlaceholdersReplacement) {
				foreach ($projectPathsPlaceholdersReplacement as $placeholder => $placeholderPath) {
					$pathsPlaceholders[$placeholder] = $placeholderPath;
				}
			}

			$this->pathsPlaceholders = $pathsPlaceholders;
		}

		foreach ($this->pathsPlaceholders as $placeholder => $placeholderPath) {
			$placeholder = self::PATH_PLACEHOLDER_DELIMITER . $placeholder . self::PATH_PLACEHOLDER_DELIMITER;
			$content = str_replace($placeholder, $placeholderPath, $content);
		}

		return $content;
	}


	/*************************** Synchronization methods ***************************/


	private function synchronizeGitignore(array $parameters): void
	{
		$gitignoreFilePath = $this->projectDirectory . '/' . self::GITIGNORE_FILE_NAME;
		Helpers::appendToFile($gitignoreFilePath, $this->createGitignoreSection($parameters));
	}


	private function desynchronizeGitignore(array $parameters): void
	{
		$gitignoreFilePath = $this->projectDirectory . '/' . self::GITIGNORE_FILE_NAME;
		Helpers::removeFromFile($gitignoreFilePath, $this->createGitignoreSection($parameters));
	}


	private function createGitignoreSection(array $parameters): string
	{
		$openTag = self::GITIGNORE_OPEN_TAG . ' ' . $this->packageName . ' ' . self::GITIGNORE_HASHTAG_CHAIN;
		$closeTag = self::GITIGNORE_CLOSE_TAG . ' ' . $this->packageName . ' ' . self::GITIGNORE_HASHTAG_CHAIN;
		$parameters = join(PHP_EOL, $parameters);
		$parameters = $this->replacePathPlaceholders($parameters);
		$packageSection = $openTag . PHP_EOL . $parameters . PHP_EOL . $closeTag . PHP_EOL;

		return $packageSection;
	}


	private function synchronizeResources(stdClass $resources): void
	{
		foreach ($resources as $resource => $targetPath) {
			$targetPath = $this->replacePathPlaceholders($targetPath);
			Helpers::copy($this->packageDirectory . '/' . $resource, $this->projectDirectory . '/' . $targetPath);
		}
	}

}
