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

namespace ComposerSynchronizer\Tests;

use Tester\Assert;
use Tester\TestCase;


abstract class AbstractTestCase extends TestCase
{

	protected final function fileContains(string $filePath, string $content): bool
	{
		$fileContent = file_get_contents($filePath);
		return strpos($fileContent, $content) !== false;
	}


	protected final function matchFile($expectedFilePath, $actualFilePath): void
	{
		$expectedFileContent = file_get_contents($expectedFilePath);
		$actualFileContent = file_get_contents($actualFilePath);
		Assert::true(is_file($actualFilePath));
		Assert::equal($expectedFileContent, $actualFileContent);
	}

}
