<?php
namespace ZeroConfig\Preacher\Installer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;
use ZeroConfig\Preacher\AppKernel;
use ZeroConfig\Preacher\Environment;

class InstallerPlugin implements PluginInterface, EventSubscriberInterface
{
    /** @var Environment */
    private $environment;

    /** @var Application */
    private $cacheClearer;

    /** @var bool */
    private $isActivated = false;

    /** @var IOInterface */
    private $prompt;

    /**
     * Get the Preacher cache clearer.
     *
     * @return Application
     */
    public function getCacheClearer(): Application
    {
        static $autoLoader = __DIR__ . '/../../../autoload.php';

        if ($this->cacheClearer === null) {
            if (file_exists($autoLoader)) {
                /** @noinspection PhpIncludeInspection */
                require $autoLoader;
            }

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

        $this->prompt      = $inputOutput;
        $this->isActivated = true;
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
        // When called outside of a Composer environment, this can be solved safely.
        if (!$this->isActivated) {
            $this->getCacheClearer()->run(
                new ArrayInput(
                    [
                        'cache:clear',
                        '--no-warmup' => true
                    ]
                )
            );
            return;
        }

        // When running inside of Composer, we have a contaminated Symfony kernel.
        // Therefore, we directly invoke the Preacher console.
        $process = new Process(
            'bin/console cache:clear',
            __DIR__ . '/../../preacher'
        );

        $process->mustRun(
            function (string $type, string $buffer): void {
                if ($type !== Process::OUT) {
                    $this->prompt->writeError($buffer, false);
                    return;
                }

                $this->prompt->write($buffer, false);
            }
        );
    }
}
