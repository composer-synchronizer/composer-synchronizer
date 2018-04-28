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

namespace ComposerSynchronizer\Tests\Nette2\InstallEventTestCase;

require_once '../bootstrap.php';

use ComposerSynchronizer\Tests\AbstractUninstallEventTestCase;
use Tester\Assert;


class UninstallEventTestCase extends AbstractUninstallEventTestCase
{

	public function testIncludesDesyncronization(): void
	{
		$filePath = 'fixtures/app/config/composer-synchronizer.neon';
		Assert::false($this->fileContains($filePath, "\t- somePackage/extension.neon"));
	}


	public function testGitignoreDesynchronization(): void
	{
		$filePath = 'fixtures/.gitignore';
		Assert::false($this->fileContains($filePath, "www/webtemp/"));
	}

}

(new UninstallEventTestCase())->run();
