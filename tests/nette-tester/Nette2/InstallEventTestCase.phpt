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
		Assert::true(is_file('actual/app/config/somePackage/extension.neon'));
		Assert::matchFile('actual/app/config/somePackage/extension.neon',
			file_get_contents('expected/install-event/extension.neon')
		);

		Assert::true(is_file('actual/temporaryFiles/cache/.gitignore'));
		Assert::matchFile('actual/temporaryFiles/cache/.gitignore',
			file_get_contents('expected/install-event/cache/.gitignore')
		);

		Assert::true(is_file('actual/www/webtemp/.gitignore'));
		Assert::matchFile('actual/www/webtemp/.gitignore',
			file_get_contents('expected/install-event/webtemp/.gitignore')
		);

	}


	public function testGitignoreSynchronization(): void
	{
		Assert::true(is_file('actual/.gitignore'));
		Assert::matchFile('actual/.gitignore',
			file_get_contents('expected/install-event/.gitignore')
		);
	}


	public function testIncludesSyncronization(): void
	{
		Assert::true(is_file('actual/app/config/composer-synchronizer.neon'));
		Assert::matchFile('actual/app/config/composer-synchronizer.neon',
			file_get_contents('expected/install-event/app/config/composer-synchronizer.neon')
		);
	}

}

(new InstallEventTestCase())->run();