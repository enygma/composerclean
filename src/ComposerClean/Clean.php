<?php

namespace ComposerClean;

use Composer\Script\CommandEvent;
use Composer\Installer\PackageEvent;

class Clean
{
	public static function exec(CommandEvent $event)
	{
		$composer = $event->getComposer();
		$installManager = $composer->getInstallationManager();
		$repoManager = $composer->getRepositoryManager();

		$packages = $repoManager->getLocalRepository()->getPackages();
		foreach ($packages as $package) {
			$path = $installManager->getInstallPath($package);
			$extra = $package->getExtra();

			if (array_key_exists('clean', $extra)) {
				// we have things to remove, try to take them out
				foreach ($extra['clean'] as $remove) {
					$resolvePath = realpath($path.'/'.$remove);
					if ($resolvePath !== false) {
						if (is_dir($resolvePath)) {
							self::unlinkDirectory($resolvePath);
							// rmdir($resolvePath);
						} elseif (is_file($resolvePath)) {
							// unlink($resolvePath);
							self::unlinkFile($resolvePath);
						}
					}
				}
			}
		}
	}

	private static function unlinkDirectory($path)
	{
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST
		);
		$iterator->rewind();
		$files = array();
		$directories = array();

		foreach ($iterator as $path => $directory) {
			if (is_file($path)) {
				$files[] = $path;
			} elseif (is_dir($path)) {
				$directories[] = $path;
			}
		}

		// Remove the files, then the directories
		foreach ($files as $filePath) {
			self::unlinkFile($filePath);
		}
		foreach ($directories as $dirPath) {
			rmdir($dirPath);
		}

		// Finally, remove the path itself
		if (is_dir($path)) {
			rmdir($path);
		}
	}
	private static function unlinkFile($path)
	{
		return unlink($path);
	}
}