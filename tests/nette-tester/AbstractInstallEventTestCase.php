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


abstract class AbstractInstallEventTestCase extends AbstractTestCase
{

	abstract public function testResourcesSynchronization(): void;

	abstract public function testGitignoreSynchronization(): void;

	abstract public function testNonLockablePackages(): void;

}
