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
	private const API_REQUEST_CONFIGURATION = [
		'http' => [
			'method' => 'GET',
			'header' => [
				'User-Agent: PHP'
			]
		]
	];

	private const API_DIRECTORY_TYPE = 'dir';
	private const API_FILE_TYPE = 'file';

	private const RAW_FILES_URL = 'https://raw.githubusercontent.com/composer-synchronizer/packages/master';

	private const PACKAGES_TEMP_DIRECTORY_NAME = 'composer-synchronizer-packages';

	/**
	 * @var string
	 */
	private $tempDirectory;

	/**
	 * @var resource
	 */
	private $apiRequestContext;

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


	public function getPackageConfiguration(PackageInterface $package): ?stdClass
	{
		$this->processedPackage = $package;
		$this->packageConfigurationDirectory = $this->vendorDirectory . '/' . $package->getPrettyName();
		$packageComposerJsonFile = $this->packageConfigurationDirectory . '/composer.json';
		$packageVersion = preg_replace('#(?:\.0)+$#', '', $package->getVersion());
		$data = null;

		if (is_file($packageComposerJsonFile)) {
			$packageComposerJsonFileContent = Helpers::loadFileContent($packageComposerJsonFile);

			if ($packageComposerJsonFileContent) {
				$packageComposerJsonFileContent = json_decode($packageComposerJsonFileContent);

				if (isset($packageComposerJsonFileContent->extra->{'composer-synchronizer'}->{$this->projectType})) {
					$data = $packageComposerJsonFileContent->extra->{'composer-synchronizer'}->{$this->projectType};
				}
			}
		}

		if ( ! $data) {
			$remoteConfigurationFilesUrlPart =
				$package->getPrettyName() . '/' . $packageVersion . '/' . $this->projectType;

			$remoteConfigurationFilesUrl = self::API_CONTENTS_URL . '/' . $remoteConfigurationFilesUrlPart;
			$remoteConfigurationConfigFileUrl =
				self::RAW_FILES_URL . '/' . $remoteConfigurationFilesUrlPart . '/config.json';

			$this->apiRequestContext = stream_context_create(self::API_REQUEST_CONFIGURATION);
			$data = Helpers::loadFileContent($remoteConfigurationConfigFileUrl, null, false);

			if ($data) {
				$this->packageConfigurationDirectory = $this->tempDirectory . '/' . $package->getPrettyName();
				$this->downloadConfigurationFilesFromGithubRepository($remoteConfigurationFilesUrl);
				$this->packageConfigurationDirectory =
					$this->packageConfigurationDirectory . '/' . $packageVersion . '/' . $this->projectType;
				$data = json_decode(Helpers::loadFileContent($this->packageConfigurationDirectory . '/config.json'));
			}
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


	private function downloadConfigurationFilesFromGithubRepository(string $url): void
	{
		$response = Helpers::loadFileContent($url, $this->apiRequestContext);

		if ( ! $response) {
			return;
		}

		$this->downloadFiles(json_decode($response));
	}


	/**
	 * @param stdClass[] $apiResponse
	 */
	private function downloadFiles(array $apiResponse): void
	{
		foreach ($apiResponse as $fileOrDirectory) {
			$fileOrDirectoryUrl = $fileOrDirectory->url;

			if ($fileOrDirectory->type === self::API_FILE_TYPE) {
				$fileOrDirectoryUrl = $fileOrDirectory->download_url;
			}

			$response = Helpers::loadFileContent($fileOrDirectoryUrl, $this->apiRequestContext);

			if ( ! $response) {
				continue;
			}

			if ($fileOrDirectory->type === self::API_DIRECTORY_TYPE) {
				$this->downloadFiles(json_decode($response));

			} elseif ($fileOrDirectory->type === self::API_FILE_TYPE) {
				$filePath = str_replace(
					self::RAW_FILES_URL . '/' . $this->processedPackage->getPrettyName() . '/', '', $fileOrDirectoryUrl
				);
				$filePath = $this->packageConfigurationDirectory . '/' . $filePath;
				Helpers::saveFile($filePath, $response);
			}
		}
	}

}
