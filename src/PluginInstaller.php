<?php
namespace ZeroConfig\Preacher\Installer;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use ZeroConfig\Preacher\Environment;

class PluginInstaller extends LibraryInstaller
{
    /** @var Environment */
    private $environment;

    /**
     * Constructor.
     *
     * @param IOInterface $inputOutput
     * @param Composer    $composer
     * @param Environment $environment
     */
    public function __construct(
        IOInterface $inputOutput,
        Composer $composer,
        Environment $environment
    ) {
        parent::__construct($inputOutput, $composer, 'preacher-plugin');
        $this->environment = $environment;
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
        $class = $this->getBundleClass($package);
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
        $class = $this->getBundleClass($package);
        parent::uninstall($repo, $package);
    }

    private function getBundleClass(PackageInterface $package): string
    {
        var_dump($package->getTargetDir(), $package->getAutoload());
        exit;
    }
}
