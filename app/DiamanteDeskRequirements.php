<?php

require_once __DIR__ . '/SymfonyRequirements.php';

use Symfony\Component\Process\ProcessBuilder;

/**
 * This class specifies all requirements that are necessary to run the DiamanteDesk Application.
 */
class DiamanteDeskRequirements extends SymfonyRequirements
{

    public function __construct()
    {
        parent::__construct();

        $baseDir = realpath(__DIR__ . '/..');

        $this->addDiamanteDeskRequirement(
            is_writable($baseDir . '/app/attachments'),
            'app/attachments/ directory must be writable',
            'Change the permissions of the "<strong>app/attachments/</strong>" directory so that the web server can write into it.'
        );

        $this->addDiamanteDeskRequirement(
            $this->IsNpmInstalled(),
            'npm package manager is installed',
            'Install <strong>npm</strong> package manager.'
        );

        $this->addDiamanteDeskRequirement(
            $this->IsGruntAndBowerInstalled(),
            'grunt and bower packages are installed',
            'Install <strong>grunt and bower</strong> packages.'
        );

    }

    /**
     * Adds an DiamanteDesk specific requirement.
     *
     * @param Boolean     $fulfilled Whether the requirement is fulfilled
     * @param string      $testMessage The message for testing the requirement
     * @param string      $helpHtml The help text formatted in HTML for resolving the problem
     * @param string|null $helpText The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addDiamanteDeskRequirement($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new DiamanteDeskRequirement($fulfilled, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Get the list of DiamanteDesk specific requirements
     *
     * @return array
     */
    public function getDiamanteDeskRequirements()
    {
        return array_filter(
            $this->getRequirements(),
            function ($requirement) {
                return $requirement instanceof DiamanteDeskRequirement;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRequirements()
    {
        return parent::getRequirements();
    }

    /**
     * @return string|null
     */
    protected function IsNpmInstalled()
    {
        $builder = new ProcessBuilder(array('npm', '-v'));
        $builder = $builder->getProcess();
        if (isset($_SERVER['PATH'])) {
            $builder->setEnv(array('PATH' => $_SERVER['PATH']));
        }
        $builder->run();
        if ($builder->getErrorOutput() === null) {
            return true;
        }

        return false;
    }

    /**
     * @return string|null
     */
    protected function IsGruntAndBowerInstalled()
    {
        $packages = array(array('grunt', '--version'), array('bower', '--version'));
        $isInstalled = true;
        foreach ($packages as $package) {
            $builder = new ProcessBuilder($package);
            $builder = $builder->getProcess();
            if (isset($_SERVER['PATH'])) {
                $builder->setEnv(array('PATH' => $_SERVER['PATH']));
            }
            $builder->run();
            if ($builder->getErrorOutput() !== null) {
                return false;
            }
        }

        return $isInstalled;
    }

}

class DiamanteDeskRequirement extends Requirement
{
}
