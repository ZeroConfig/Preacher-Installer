<?php
namespace ZeroConfig\Preacher\Installer;

use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;

interface PluginManagerInterface
{
    /**
     * Remove the given plugin from the installed list of plugins.
     *
     * @param PackageInterface $package
     *
     * @return void
     */
    public function removePlugin(PackageInterface $package);

    /**
     * Add the given plugin to the installed list of plugins.
     *
     * @param PackageInterface $package
     *
     * @return void
     */
    public function addPlugin(PackageInterface $package);

    /**
     * Export the plugins using the given file system.
     *
     * @param Filesystem $fileSystem
     *
     * @return void
     */
    public function exportPlugins(Filesystem $fileSystem);
}
