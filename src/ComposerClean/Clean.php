<?php

namespace ComposerClean;

use Composer\Script\CommandEvent;
use Composer\Installer\PackageEvent;

class Clean
{
	public static function exec(CommandEvent $event)
	{
		// echo $event->getName()."\n";

		$composer = $event->getComposer();
		$installManager = $composer->getInstallationManager();
		$repoManager = $composer->getRepositoryManager();

		$packages = $repoManager->getLocalRepository()->getPackages();
		foreach ($packages as $package) {
			$path = $installManager->getInstallPath($package);
			echo 'path: '.$path."\n";

			echo 'class: '.get_class($package)." - ".$package->getName()." :: ".print_r($package->getAutoload(), true)."\n";
			$extra = $package->getExtra();

			if (array_key_exists('clean', $extra)) {
				echo 'extra: '; print_r($extra);

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
			echo $filePath."\n";
			self::unlinkFile($filePath);
		}
		foreach ($directories as $dirPath) {
			echo $dirPath."\n";
			rmdir($dirPath);
		}

		// Finally, remove the path itself
		rmdir($path);
	}
	private static function unlinkFile($path)
	{
		return unlink($path);
	}

	public function command()
	{
		echo 'command';
	}
}