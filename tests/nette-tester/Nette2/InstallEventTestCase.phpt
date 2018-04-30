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
		Assert::true(is_file('fixtures/app/config/somePackage/extension.neon'));
		Assert::matchFile('fixtures/app/config/somePackage/extension.neon',
			file_get_contents('expected/install-event/extension.neon')
		);

		Assert::true(is_file('fixtures/temporaryFiles/cache/.gitignore'));
		Assert::matchFile('fixtures/temporaryFiles/cache/.gitignore',
			file_get_contents('expected/install-event/cache/.gitignore')
		);

		Assert::true(is_file('fixtures/www/webtemp/.gitignore'));
		Assert::matchFile('fixtures/www/webtemp/.gitignore',
			file_get_contents('expected/install-event/webtemp/.gitignore')
		);

	}


	public function testGitignoreSynchronization(): void
	{
		Assert::true(is_file('fixtures/.gitignore'));
		Assert::matchFile('fixtures/.gitignore',
			file_get_contents('expected/install-event/.gitignore')
		);
	}


	public function testIncludesSyncronization(): void
	{
		Assert::true(is_file('fixtures/app/config/composer-synchronizer.neon'));
		Assert::matchFile('fixtures/app/config/composer-synchronizer.neon',
			file_get_contents('expected/install-event/app/config/composer-synchronizer.neon')
		);
	}

}

(new InstallEventTestCase())->run();