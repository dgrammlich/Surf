<?php
namespace TYPO3\Surf\Task\Git;

/*                                                                        *
 * This script belongs to the TYPO3 project "TYPO3 Surf"                  *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Exception\InvalidConfigurationException;

/**
 * Push to a git remote.
 *
 * It takes the following options:
 *
 * * remote - The git remote to use.
 * * refspec - The refspec to push.
 * * recurseIntoSubmodules (optional) - If true, push submodules as well.
 *
 * Example:
 *  $workflow
 *      ->setTaskOptions('TYPO3\Surf\Task\Git\PushTask', [
 *              'remote' => 'git@github.com:TYPO3/Surf.git',
 *              'refspec' => 'master',
 *              'recurseIntoSubmodules' => true
 *          ]
 *      );
 */
class PushTask extends \TYPO3\Surf\Domain\Model\Task implements \TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface
{
    use \TYPO3\Surf\Domain\Service\ShellCommandServiceAwareTrait;

    /**
     * Execute this task
     *
     * @param \TYPO3\Surf\Domain\Model\Node $node
     * @param \TYPO3\Surf\Domain\Model\Application $application
     * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
     * @param array $options
     * @return void
     * @throws \TYPO3\Surf\Exception\InvalidConfigurationException
     */
    public function execute(Node $node, Application $application, Deployment $deployment, array $options = array())
    {
        if (!isset($options['remote'])) {
            throw new InvalidConfigurationException('Missing "remote" option for PushTask', 1314186541);
        }

        if (!isset($options['refspec'])) {
            throw new InvalidConfigurationException('Missing "refspec" option for PushTask', 1314186553);
        }

        $targetPath = $deployment->getApplicationReleasePath($application);

        $this->shell->executeOrSimulate(sprintf('cd ' . $targetPath . '; git push -f %s %s', $options['remote'], $options['refspec']), $node, $deployment);
        if (isset($options['recurseIntoSubmodules']) && $options['recurseIntoSubmodules'] === true) {
            $this->shell->executeOrSimulate(sprintf('cd ' . $targetPath . '; git submodule foreach \'git push -f %s %s\'', $options['remote'], $options['refspec']), $node, $deployment);
        }
    }

    /**
     * Simulate this task
     *
     * @param Node $node
     * @param Application $application
     * @param Deployment $deployment
     * @param array $options
     * @return void
     */
    public function simulate(Node $node, Application $application, Deployment $deployment, array $options = array())
    {
        $this->execute($node, $application, $deployment, $options);
    }
}
