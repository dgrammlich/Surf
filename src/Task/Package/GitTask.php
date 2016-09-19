<?php
namespace TYPO3\Surf\Task\Package;

/*                                                                        *
 * This script belongs to the TYPO3 project "TYPO3 Surf"                  *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Task\Git\AbstractCheckoutTask;

/**
 * A Git package task.
 *
 * Package an application by doing a local git update / clone before using the configured "transferMethod" to transfer
 * assets to the application node(s).
 *
 * It takes the following options:
 *
 * * repositoryUrl - The git remote to use.
 * * fetchAllTags (optional) - If true, make a fetch on tags.
 * * gitPostCheckoutCommands (optional) - An array with commands to execute after checkout.
 * * hardClean (optional) - If true, execute a hard clean.
 * * recursiveSubmodules (optional) - If true, handle submodules recursive.
 * * verbose (optional) - If true, output verbose information from git.
 *
 * Example:
 *  $workflow
 *      ->setTaskOptions('TYPO3\Surf\Task\Package\GitTask', [
 *                  'repositoryUrl' => 'git@github.com:TYPO3/Surf.git',
 *                  'verbose' => true,
 *                  'recursiveSubmodules' => true,
 *                  'fetchAllTags' => true,
 *                  'hardClean' => true,
 *                  'gitPostCheckoutCommands' => [
 *                      '/var/www/outerspace' => 'composer install'
 *                  ]
 *              ]
 *          ]
 *      );
 */
class GitTask extends AbstractCheckoutTask
{
    /**
     * Execute this task
     *
     * @param \TYPO3\Surf\Domain\Model\Node $node
     * @param \TYPO3\Surf\Domain\Model\Application $application
     * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
     * @param array $options
     * @return void
     * @throws \TYPO3\Surf\Exception\InvalidConfigurationException
     * @throws \TYPO3\Surf\Exception\TaskExecutionException
     */
    public function execute(Node $node, Application $application, Deployment $deployment, array $options = array())
    {
        if (!isset($options['repositoryUrl'])) {
            throw new \TYPO3\Surf\Exception\InvalidConfigurationException(sprintf('Missing "repositoryUrl" option for application "%s"', $application->getName()), 1374074052);
        }

        $localCheckoutPath = $deployment->getWorkspacePath($application);

        $node = $deployment->getNode('localhost');

        $sha1 = $this->executeOrSimulateGitCloneOrUpdate($localCheckoutPath, $node, $deployment, $options);

        $this->executeOrSimulatePostGitCheckoutCommands($localCheckoutPath, $sha1, $node, $deployment, $options);
    }
}
