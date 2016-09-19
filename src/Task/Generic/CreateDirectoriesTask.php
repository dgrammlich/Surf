<?php
namespace TYPO3\Surf\Task\Generic;

/*                                                                        *
 * This script belongs to the TYPO3 project "TYPO3 Surf"                  *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;

/**
 * Creates directories for a release.
 *
 * It takes the following options:
 *
 * * baseDirectory (optional) - Can be set as base path.
 * * directories - An array of directories to create. The paths can be relative to the baseDirectory, if set.
 *
 * Example:
 *  $workflow
 *      ->setTaskOptions('TYPO3\Surf\Task\Generic\CreateDirectoriesTask', [
 *              'baseDirectory' => '/var/www/outerspace',
 *              'directories' => [
 *                  'uploads/spaceship',
 *                  'uploads/freighter',
 *                  '/tmp/outerspace/lonely_planet'
 *              ]
 *          ]
 *      );
 */
class CreateDirectoriesTask extends \TYPO3\Surf\Domain\Model\Task implements \TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface
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
     */
    public function execute(Node $node, Application $application, Deployment $deployment, array $options = array())
    {
        if (!isset($options['directories']) || !is_array($options['directories']) || $options['directories'] === array()) {
            return;
        }

        $baseDirectory = isset($options['baseDirectory']) ? $options['baseDirectory'] : $deployment->getApplicationReleasePath($application);

        $commands = array(
            'cd ' . $baseDirectory
        );
        foreach ($options['directories'] as $path) {
            $commands[] = 'mkdir -p ' . $path;
        }

        $this->shell->executeOrSimulate($commands, $node, $deployment);
    }

    /**
     * Simulate this task
     *
     * @param \TYPO3\Surf\Domain\Model\Node $node
     * @param \TYPO3\Surf\Domain\Model\Application $application
     * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
     * @param array $options
     * @return void
     */
    public function simulate(Node $node, Application $application, Deployment $deployment, array $options = array())
    {
        $this->execute($node, $application, $deployment, $options);
    }
}
