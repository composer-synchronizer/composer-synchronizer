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


final class InstallEventTestCase extends AbstractInstallEventTestCase
{

	public function testResourcesSynchronization(): void
	{
		$this->matchFile('expected/install-event/extension.neon', 'actual/app/config/somePackage/extension.neon');

		$this->matchFile('expected/install-event/cache/.gitignore', 'actual/temporaryFiles/cache/.gitignore');

		$this->matchFile('expected/install-event/webtemp/.gitignore', 'actual/www/webtemp/.gitignore');
	}


	public function testGitignoreSynchronization(): void
	{
		$this->matchFile('expected/install-event/.gitignore', 'actual/.gitignore');
	}


	public function testNonLockablePackages(): void
	{
		$this->matchFile(
			'expected/install-event/composer-synchronizer.lock',
			'actual/composer-synchronizer.lock'
		);
	}


	public function testIncludesSyncronization(): void
	{
		$this->matchFile(
			'expected/install-event/app/config/composer-synchronizer.neon',
			'actual/app/config/composer-synchronizer.neon'
		);
	}

}

(new InstallEventTestCase())->run();
