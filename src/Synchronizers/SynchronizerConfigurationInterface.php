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


interface SynchronizerConfigurationInterface
{

	function init(): SynchronizerConfigurationInterface;


	function synchronize(): void;


	function setComposerEventType(string $type): SynchronizerConfigurationInterface;


	function setPackageConfiguration(stdClass $configuration): SynchronizerConfigurationInterface;


	function setPackageDirectory(string $directory): SynchronizerConfigurationInterface;


	function setPackageName(string $name): SynchronizerConfigurationInterface;


	function setProjectConfiguration(stdClass $configuration): SynchronizerConfigurationInterface;


	function setProjectDirectory(string $directory): SynchronizerConfigurationInterface;

}
