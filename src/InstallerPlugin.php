<?php
namespace ZeroConfig\Preacher\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use ZeroConfig\Preacher\Environment;

class InstallerPlugin implements PluginInterface
{
    /**
     * Apply plugin modifications to Composer.
     *
     * @param Composer    $composer
     * @param IOInterface $inputOutput
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $inputOutput)
    {
        $environment = new Environment();

        $composer
            ->getInstallationManager()
            ->addInstaller(
                new PluginInstaller(
                    $inputOutput,
                    $composer,
                    new PluginManager($environment)
                )
            );
    }
}
