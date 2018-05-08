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


final class UninstallEventTestCase extends AbstractUninstallEventTestCase
{

	public function testIncludesDesyncronization(): void
	{
		$this->matchFile(
			'expected/uninstall-event/app/config/composer-synchronizer.neon',
			'actual/app/config/composer-synchronizer.neon'
		);
	}


	public function testGitignoreDesynchronization(): void
	{
		$this->matchFile('expected/uninstall-event/.gitignore', 'actual/.gitignore');
	}

}

(new UninstallEventTestCase())->run();
