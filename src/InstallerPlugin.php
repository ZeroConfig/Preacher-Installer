<?php
namespace ZeroConfig\Preacher\Installer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use ZeroConfig\Preacher\AppKernel;
use ZeroConfig\Preacher\Environment;

class InstallerPlugin implements PluginInterface, EventSubscriberInterface
{
    /** @var Environment */
    private $environment;

    /** @var Application */
    private $cacheClearer;

    /**
     * Get the Preacher cache clearer.
     *
     * @return Application
     */
    public function getCacheClearer(): Application
    {
        if ($this->cacheClearer === null) {
            $this->cacheClearer = new Application(
                new AppKernel('prod', true)
            );
            $this->cacheClearer->setDefaultCommand('cache:clear', true);
        }

        return $this->cacheClearer;
    }

    /**
     * Get the Preacher environment.
     *
     * @return Environment
     */
    private function getEnvironment(): Environment
    {
        if ($this->environment === null) {
            $this->environment = new Environment();
        }

        return $this->environment;
    }

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
        $composer
            ->getInstallationManager()
            ->addInstaller(
                new PluginInstaller(
                    $inputOutput,
                    $composer,
                    new PluginManager(
                        $this->getEnvironment()
                    )
                )
            );
    }

    /**
     * Get a list of subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'post-install-cmd' => 'clearCache',
            'post-update-cmd' => 'clearCache'
        ];
    }

    /**
     * Clear the Preacher cache.
     *
     * @return void
     */
    public function clearCache()
    {
        $this->getCacheClearer()->run(
            new ArrayInput(['--no-warmup'])
        );
    }
}
