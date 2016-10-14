<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface as Bundle;

/**
 * Frontier Kernel.
 */
class AppKernel extends Kernel
{

    private $dynamicBundles = array();

    /**
     * Get a list bundle instances to register.
     *
     * @throws LogicException In dev or test mode when bundles were registered dynamically that are not actually added.
     * @return array
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new EyeOpen\DictionaryBundle\EyeOpenDictionaryBundle(),
            new EyeOpen\DictionaryViewBundle\EyeOpenDictionaryViewBundle(),
            new EyeOpen\DictionaryAdminBundle\EyeOpenDictionaryAdminBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            //Symfony routing component requires a lot of recursion for longer URLs
            ini_set('xdebug.max_nesting_level', 300);
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();

            if (count($this->dynamicBundles)) {
                $classNames = array();
                foreach ($bundles as $bundle) {
                    $classNames[get_class($bundle)] = get_class($bundle);
                }
                foreach ($this->dynamicBundles as $bundle) {
                    if (!isset($classNames[get_class($bundle)])) {
                        throw new LogicException(sprintf("Dynamically registered bundle '%s' would not actually be registered", get_class($bundle)));
                    }
                }
            }
        }

        return $bundles;
    }

    /**
     * Programatically add a bundle to be registered.
     *
     * This actually just makes sure the bundle is added statically later.
     *
     * @param Bundle $dynamicBundle
     *
     * @return AppKernel
     */
    public function addDynamicBundle(Bundle $dynamicBundle)
    {
        $this->dynamicBundles[] = $dynamicBundle;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    /**
     * Get the cache dir. Returns memory mounted dir if in the vagrant instance.
     *
     * @return string
     */
    public function getCacheDir()
    {
        if (getenv('IS_VAGRANT') === 'true' || getenv('user') == 'vagrant') {
            return sys_get_temp_dir() . '/frontier/cache/' .  $this->environment;
        }

        return parent::getCacheDir();
    }

    /**
     * Get the log dir. Returns memory mounted dir if in the vagrant instance.
     *
     * @return string
     */
    public function getLogDir()
    {
        if (getenv('IS_VAGRANT') === 'true' || getenv('user') == 'vagrant') {
            return sys_get_temp_dir() . '/frontier/logs';
        }

        return parent::getLogDir();
    }
}
