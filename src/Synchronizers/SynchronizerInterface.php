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

namespace ComposerSynchronizer\Synchronizers;

use stdClass;


interface SynchronizerInterface
{

	function init(): SynchronizerInterface;


	static function getAliases(): array;


	static function getVersionedName(): string;


	function synchronize(): void;


	function setComposerEventType(string $type): SynchronizerInterface;


	function setPackageConfiguration(stdClass $configuration): SynchronizerInterface;


	function setProjectConfiguration(stdClass $configuration): SynchronizerInterface;


	function setProjectDirectory(string $directory): SynchronizerInterface;

}
