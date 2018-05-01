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
		Assert::true(is_file('actual/app/config/composer-synchronizer.neon'));
		Assert::matchFile('actual/app/config/composer-synchronizer.neon',
			file_get_contents('expected/uninstall-event/app/config/composer-synchronizer.neon')
		);
	}


	public function testGitignoreDesynchronization(): void
	{
		Assert::true(is_file('actual/.gitignore'));
		Assert::matchFile('actual/.gitignore',
			file_get_contents('expected/uninstall-event/.gitignore')
		);
	}

}

(new UninstallEventTestCase())->run();
