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

use Composer\IO\IOInterface;


final class Helpers
{

	/**
	 * @var IOInterface
	 */
	private static $io;


	public static function setIo(IOInterface $io): void
	{
		self::$io = $io;
	}


	public static function consoleMessage(string $message, array $parameters = []): void
	{
		self::$io->writeError(vsprintf('  - ' . $message, $parameters), true);
	}

	/*************************** Files ***************************/


	public static function appendToFile(string $filePath, string $content, bool $withoutDuplicates = true): void
	{
		if ($withoutDuplicates && self::fileContains($filePath, $content)) {
			return;
		}

		self::insertIntoFile($filePath, $content, FILE_APPEND);
	}


	public static function insertIntoFile(string $filePath, string $content, ?int $flags = null): void
	{
		file_put_contents($filePath, $content, $flags | LOCK_EX);
	}


	public static function removeFromFile(string $filePath, string $content): void
	{
		$fileContent = self::loadFileContent($filePath);
		$fileContent = str_replace($content, '', $fileContent);
		self::insertIntoFile($filePath, $fileContent);
	}


	public static function fileContains(string $filePath, string $content): bool
	{
		if ( ! self::fileExists($filePath)) {
			return false;
		}

		$fileContent = self::loadFileContent($filePath);
		return strpos($fileContent, $content) !== false;
	}


	public static function loadFileContent(string $filePath): ?string
	{
		self::fileExists($filePath, true);
		return file_get_contents($filePath);
	}


	public static function fileExists(string $filePath, bool $throwException = false): bool
	{
		$fileExists = file_exists($filePath);

		if ($throwException && ! $fileExists) {
			throw new SynchronizerException('File ' . $filePath . ' not found.');
		}

		return $fileExists;
	}


	/**
	 * Copies files recursively and automatically creates nested directories
	 */
	public static function copy(string $source, string $dest, bool $override = false): void
	{
		$diretoryToCreate = $dest;

		if (is_file($source)) {
			$diretoryToCreate = dirname($dest);
		}

		if ( ! file_exists($diretoryToCreate) && ! is_dir($diretoryToCreate)) {
			mkdir($diretoryToCreate, 0777, true);
		}

		if (is_file($source)) {
			self::copyFile($source, $dest, $override);
			return;
		}

		$sourceHandle = opendir($source);

		if ( ! $sourceHandle) {
			throw new SynchronizerException('Failed to copy directory: failed to open source ' . $source);
		}

		while ($file = readdir($sourceHandle)) {
			if (in_array($file, ['.', '..'])) {
				continue;
			}

			if (is_dir($source . '/' . $file)) {
				if ( ! file_exists($dest . '/' . $file)) {
					mkdir($dest . '/' . $file, 0755);
				}

				self::copy($source . '/' . $file, $dest . '/' . $file, $override);

			} else {
				self::copyFile($source . '/' . $file, $dest . '/' . $file, $override);
			}
		}
	}


	private static function copyFile(string $source, string $dest, bool $override): void
	{
		if ($override || ! file_exists($dest)) {
			copy($source,  $dest);
		}
	}

}
