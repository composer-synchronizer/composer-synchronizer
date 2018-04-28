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

use ComposerSynchronizer\Tests\AbstractInstallEventTestCase;
use Tester\Assert;


class InstallEventTestCase extends AbstractInstallEventTestCase
{

	public function testResourcesSynchronization(): void
	{
		Assert::true(is_file('fixtures/app/config/composer-synchronizer.neon'));
		Assert::true(is_file('fixtures/app/config/somePackage/extension.neon'));
		Assert::true(is_file('fixtures/www/webtemp/.gitignore'));
	}


	public function testGitignoreSynchronization(): void
	{
		$filePath = 'fixtures/.gitignore';
		Assert::true($this->fileContains($filePath, "www/webtemp/"));
	}


	public function testIncludesSyncronization(): void
	{
		$filePath = 'fixtures/app/config/composer-synchronizer.neon';
		Assert::true($this->fileContains($filePath, "\t- somePackage/extension.neon"));
	}

}

(new InstallEventTestCase())->run();