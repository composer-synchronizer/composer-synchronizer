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
use stdClass;


final class PackagesManager
{

	private const API_CONTENTS_URL = 'https://api.github.com/repos/composer-synchronizer/packages/contents';
	private const RAW_FILES_URL = 'https://raw.githubusercontent.com/composer-synchronizer/packages/master';

	private const API_REQUEST_CONFIGURATION = [
		'http' => [
			'method' => 'GET',
			'header' => [
				'User-Agent: PHP'
			],
			'timeout' => 15
		]
	];

	private const API_DIRECTORY_TYPE = 'dir';
	private const API_FILE_TYPE = 'file';

	private const PACKAGES_TEMP_DIRECTORY_NAME = 'composer-synchronizer-packages';

	/**
	 * @var string
	 */
	private $composerEventType;

	/**
	 * @var string
	 */
	private $packageConfigurationDirectory;

	/**
	 * @var PackageInterface
	 */
	private $processedPackage;

	/**
	 * @var string
	 */
	private $projectType;

	/**
	 * @var bool
	 */
	private $remoteConfigurationFilesDownloadFailed = false;

	/**
	 * @var bool
	 */
	private $remoteConfigurationsEnabled = true;

	/**
	 * @var string
	 */
	private $tempDirectory;

	/**
	 * @var string
	 */
	private $vendorDirectory;


	public function __construct()
	{
		$this->tempDirectory = sys_get_temp_dir() . '/' . self::PACKAGES_TEMP_DIRECTORY_NAME;

		if ( ! is_dir($this->tempDirectory)) {
			Helpers::createDirectory($this->tempDirectory);
		}
	}


	public function disableRemoteConfigurations(): PackagesManager
	{
		$this->remoteConfigurationsEnabled = false;
		return $this;
	}


	public function getPackageConfiguration(PackageInterface $package): ?stdClass
	{
		$this->processedPackage = $package;
		$this->packageConfigurationDirectory = $this->vendorDirectory . '/' . $package->getPrettyName();
		$data = $this->getPackageLocalConfiguration($this->packageConfigurationDirectory . '/composer.json');

		if ( ! $data) {
			$data = $this->getPackageRemoteConfiguration($package);
		}

		if ($data) {
			return (object) [
				'packageName' => $package->getName(),
				'packageType' => $package->getType(),
				'synchronizerConfiguration' => $data,
				'synchronizerConfigurationDirectory' => $this->packageConfigurationDirectory
			];
		}

		return null;
	}


	public function setComposerEventType(string $type): PackagesManager
	{
		$this->composerEventType = $type;
		return $this;
	}


	public function setProjectType(string $type): PackagesManager
	{
		$this->projectType = $type;
		return $this;
	}


	public function setVendorDirectory(string $path): PackagesManager
	{
		$this->vendorDirectory = $path;
		return $this;
	}


	/**
	 * @param resource $apiRequestContext
	 * @param stdClass[] $apiResponse
	 */
	private function downloadFiles($apiRequestContext, array $apiResponse, string $temporaryDirectory): void
	{
		foreach ($apiResponse as $fileOrDirectory) {
			if ($this->remoteConfigurationFilesDownloadFailed) {
				break;
			}

			$fileOrDirectoryUrl = $fileOrDirectory->url;

			if ($fileOrDirectory->type === self::API_FILE_TYPE) {
				$fileOrDirectoryUrl = $fileOrDirectory->download_url;
			}

			$response = Helpers::loadFileContent($fileOrDirectoryUrl, $apiRequestContext);

			if ( ! $response) {
				$this->remoteConfigurationFilesDownloadFailed = true;
				break;
			}

			if ($fileOrDirectory->type === self::API_DIRECTORY_TYPE) {
				$this->downloadFiles($apiRequestContext, json_decode($response), $temporaryDirectory);

			} elseif ($fileOrDirectory->type === self::API_FILE_TYPE) {
				$filePath = str_replace(
					self::RAW_FILES_URL . '/' . $this->processedPackage->getPrettyName() . '/', '', $fileOrDirectoryUrl
				);

				Helpers::saveFile($temporaryDirectory . '/' . $filePath, $response);
			}
		}
	}


	private function downloadRemoteConfigurationFiles(string $url, string $temporaryDirectory): void
	{
		$apiRequestContext = stream_context_create(self::API_REQUEST_CONFIGURATION);
		$response = Helpers::loadFileContent($url, $apiRequestContext);

		if ( ! $response) {
			return;
		}

		$this->downloadFiles($apiRequestContext, json_decode($response), $temporaryDirectory);

		if ($this->remoteConfigurationFilesDownloadFailed) {
			Helpers::deleteFile($temporaryDirectory . '/config.json');
		}
	}


	private function getPackageLocalConfiguration(string $packageComposerJsonFile): ?stdClass
	{
		if ( ! is_file($packageComposerJsonFile)) {
			return null;
		}

		$data = null;
		$packageComposerJsonFileContent = Helpers::loadFileContent($packageComposerJsonFile);

		if ($packageComposerJsonFileContent) {
			$packageComposerJsonFileContent = json_decode($packageComposerJsonFileContent);

			if (isset($packageComposerJsonFileContent->extra->{'composer-synchronizer'})) {
				$packageConfigurationData = Helpers::getProperty(
					$packageComposerJsonFileContent->extra->{'composer-synchronizer'}, $this->projectType
				);

				if ($packageConfigurationData) {
					$data = $packageConfigurationData;
				}
			}
		}

		return $data;
	}


	private function getPackageRemoteConfiguration(PackageInterface $package): ?stdClass
	{
		if ( ! $this->remoteConfigurationsEnabled) {
			return null;
		}

		preg_match('#^(?<version>\d+\.\d+)#', $package->getVersion(), $matches);
		$packageVersion = Helpers::getProperty($matches, 'version');
		$data = null;

		if ( ! $packageVersion) {
			$packageVersion = $package->getVersion();
		}

		$packageTemporaryDirectory = $this->tempDirectory . '/' . $package->getPrettyName();
		$packageTemporaryConfigurationFilePath =
			$packageTemporaryDirectory . '/' . $packageVersion . '/' . $this->projectType . '/config.json';

		if ( ! $data
			&& $this->composerEventType === Plugin::UNINSTALL_EVENT_TYPE
			&& file_exists($packageTemporaryConfigurationFilePath)
		) {
			$data = json_decode(Helpers::loadFileContent($packageTemporaryConfigurationFilePath));

		} elseif ( ! $data) {
			$packageFilesPathPart = $packageVersion . '/' . $this->projectType;
			$packageRemoteConfigurationFilesUrlPart = $package->getPrettyName() . '/' . $packageFilesPathPart;
			$packageRemoteConfigurationFilesUrl =
				self::API_CONTENTS_URL . '/' . $packageRemoteConfigurationFilesUrlPart;

			$packageRemoteConfigurationConfigFileUrl =
				self::RAW_FILES_URL . '/' . $packageRemoteConfigurationFilesUrlPart . '/config.json';

			$packageRemoteConfigFileContent = Helpers::loadFileContent(
				$packageRemoteConfigurationConfigFileUrl, null, false
			);

			if ($packageRemoteConfigFileContent) {
				$this->packageConfigurationDirectory = $packageTemporaryDirectory . '/' . $packageFilesPathPart;
				$this->downloadRemoteConfigurationFiles(
					$packageRemoteConfigurationFilesUrl, $packageTemporaryDirectory
				);

				if ( ! $this->remoteConfigurationFilesDownloadFailed) {
					$packageTemporaryConfigurationFileContent = Helpers::loadFileContent(
						$packageTemporaryConfigurationFilePath
					);

					if ($packageTemporaryConfigurationFileContent) {
						$data = json_decode($packageTemporaryConfigurationFileContent);
					}
				}
			}
		}

		return $data;
	}

}
