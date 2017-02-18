<?php
namespace ZeroConfig\Preacher\Installer;

use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;
use SplFileObject;
use ZeroConfig\Preacher\Environment;

class PluginManager implements PluginManagerInterface
{
    /** @var Environment */
    private $environment;

    /** @var string[] */
    private $bundles;

    /**
     * Constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $file = $this->environment->getPluginConfigurationFile();

        /** @noinspection PhpIncludeInspection */
        $this->bundles = file_exists($file)
            ? include $file
            : [];
    }

    /**
     * Get the bundle class for the given package.
     *
     * @param PackageInterface $package
     *
     * @return string
     */
    private function getBundleClass(PackageInterface $package): string
    {
        $class = '';
        $extra = $package->getExtra();

        if (!empty($extra['class'])) {
            $class = $extra['class'];
        }

        return $class;
    }

    /**
     * Remove the given plugin from the installed list of plugins.
     *
     * @param PackageInterface $package
     *
     * @return void
     */
    public function removePlugin(PackageInterface $package)
    {
        $class  = $this->getBundleClass($package);
        $offset = array_search($class, $this->bundles);

        if ($offset !== false) {
            unset($this->bundles[$offset]);
        }
    }

    /**
     * Add the given plugin to the installed list of plugins.
     *
     * @param PackageInterface $package
     *
     * @return void
     */
    public function addPlugin(PackageInterface $package)
    {
        $class = $this->getBundleClass($package);

        if (!empty($class) && !in_array($class, $this->bundles, true)) {
            array_push($this->bundles, $class);
        }
    }

    /**
     * Export the plugins using the given file system.
     *
     * @param Filesystem $fileSystem
     *
     * @return void
     */
    public function exportPlugins(Filesystem $fileSystem)
    {
        $file = $this->environment->getPluginConfigurationFile();
        $fileSystem->ensureDirectoryExists(dirname($file));

        $writer = new SplFileObject($file, 'w+');
        $writer->fwrite(
            sprintf(
                '<?php return %s;',
                var_export($this->bundles, true)
            )
        );
        unset($writer);
    }
}
