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


	/**
	 * Copies files recursively and automatically creates nested directories
	 */
	public static function copy(string $source, string $dest, bool $override = false): void
	{
		if (is_file($source)) {
			self::copyFile($source, $dest, $override);
			return;
		}

		$sourceHandle = opendir($source);

		if ( ! $sourceHandle) {
			throw new SynchronizerException('Failed to copy directory: failed to open source ' . $source);
		}

		while ($file = readdir($sourceHandle)) {
			if (in_array($file, ['.', '..'], true)) {
				continue;
			}

			if (is_dir($source . '/' . $file)) {
				if ( ! file_exists($dest . '/' . $file)) {
					mkdir($dest . '/' . $file, 0755);
				}

				$file .= '/';
				self::copy($source .  $file, $dest . $file, $override);

			} else {
				$dest = rtrim($dest, '/') . '/';
				$source = rtrim($source, '/') . '/';
				self::copyFile($source . $file, $dest . $file, $override);
			}
		}
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


	public static function loadFileContent(string $filePath, $context = null, $consoleMessage = true): ?string
	{
		$fileContent = @file_get_contents($filePath, false, $context);

		if ($fileContent === false) {
			if ($consoleMessage) {
				self::consoleMessage('File ' . $filePath . ' wasn\'t found or could not be loaded.');
			}
			return null;
		};

		return $fileContent;
	}


	public static function fileExists(string $filePath): bool
	{
		return file_exists($filePath);
	}


	public static function createDirectory(string $directory): void
	{
		mkdir($directory, 0777, true);
	}


	public static function saveFile(string $file, string $content): void
	{
		$pathInfo = pathinfo($file);

		if ( ! is_dir($pathInfo['dirname'])) {
			self::createDirectory($pathInfo['dirname']);
		}

		file_put_contents($file, $content);
	}


	private static function copyFile(string $source, string $dest, bool $override): void
	{
		if ($override || ! is_file($dest)) {
			$sourcePathInfo = pathinfo($source);
			$destPathInfo = pathinfo($dest);
			$destEndsWithSlash = preg_match('#\/$#', $dest);

			if ($destEndsWithSlash && ! is_dir($dest)) {
				self::createDirectory($dest);

			} elseif (is_file($source) && ! is_dir($destPathInfo['dirname'])) {
				self::createDirectory($destPathInfo['dirname']);
			}

			if (is_file($source) && preg_match('#\/$#', $dest)) {
				$dest .= $sourcePathInfo['basename'];
			}

			copy($source,  $dest);
		}
	}

}
