<?php
namespace ZeroConfig\Preacher\Installer;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class PluginInstaller extends LibraryInstaller
{
    /** @var PluginManagerInterface */
    private $pluginManager;

    /**
     * Constructor.
     *
     * @param IOInterface            $inputOutput
     * @param Composer               $composer
     * @param PluginManagerInterface $pluginManager
     */
    public function __construct(
        IOInterface $inputOutput,
        Composer $composer,
        PluginManagerInterface $pluginManager
    ) {
        parent::__construct($inputOutput, $composer, 'preacher-plugin');
        $this->pluginManager = $pluginManager;
    }

    /**
     * Install the given plugin.
     *
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface             $package
     *
     * @return void
     */
    public function install(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {
        parent::install($repo, $package);
        $this->pluginManager->addPlugin($package);
        $this->pluginManager->exportPlugins($this->filesystem);
    }

    /**
     * Update the given plugin.
     *
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface             $initial
     * @param PackageInterface             $target
     *
     * @return void
     */
    public function update(
        InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ) {
        parent::update($repo, $initial, $target);
        $this->pluginManager->removePlugin($initial);
        $this->pluginManager->addPlugin($target);
        $this->pluginManager->exportPlugins($this->filesystem);
    }

    /**
     * Remove the given plugin.
     *
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface             $package
     *
     * @return void
     */
    public function uninstall(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {
        parent::uninstall($repo, $package);
        $this->pluginManager->removePlugin($package);
        $this->pluginManager->exportPlugins($this->filesystem);
    }
}
