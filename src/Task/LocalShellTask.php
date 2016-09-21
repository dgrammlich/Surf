<?php
namespace TYPO3\Surf\Task;

/*                                                                        *
 * This script belongs to the TYPO3 project "TYPO3 Surf"                  *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;

/**
 * A shell task for local packaging.
 *
 * It takes the following options:
 *
 * * command - The command to execute.
 * * rollbackCommand (optional) - The command to execute as a rollback.
 * * ignoreErrors (optional) - If true, ignore errors during execution. Default is true.
 * * logOutput (optional) - If true, output the log. Default is false.
 *
 * Example:
 *  $workflow
 *      ->setTaskOptions('TYPO3\Surf\Task\LocalShellTask', [
 *              'command' => mkdir -p /var/wwww/outerspace',
 *              'rollbackCommand' => 'rm -rf /Var/www/outerspace'
 *          ]
 *      );
 */
class LocalShellTask extends \TYPO3\Surf\Domain\Model\Task implements \TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface
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
        $replacePaths = array();
        $replacePaths['{workspacePath}'] = escapeshellarg($deployment->getWorkspacePath($application));

        if (!isset($options['command'])) {
            throw new \TYPO3\Surf\Exception\InvalidConfigurationException('Missing "command" option for LocalShellTask', 1311168045);
        }
        $command = $options['command'];
        $command = str_replace(array_keys($replacePaths), $replacePaths, $command);

        $ignoreErrors = isset($options['ignoreErrors']) && $options['ignoreErrors'] === true;
        $logOutput = !(isset($options['logOutput']) && $options['logOutput'] === false);

        $localhost = new Node('localhost');
        $localhost->setHostname('localhost');

        $this->shell->executeOrSimulate($command, $localhost, $deployment, $ignoreErrors, $logOutput);
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

    /**
     * Rollback this task
     *
     * @param \TYPO3\Surf\Domain\Model\Node $node
     * @param \TYPO3\Surf\Domain\Model\Application $application
     * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
     * @param array $options
     * @return void
     */
    public function rollback(Node $node, Application $application, Deployment $deployment, array $options = array())
    {
        $replacePaths = array();
        $replacePaths['{workspacePath}'] = escapeshellarg($deployment->getWorkspacePath($application));

        if (!isset($options['rollbackCommand'])) {
            return;
        }
        $command = $options['rollbackCommand'];
        $command = str_replace(array_keys($replacePaths), $replacePaths, $command);

        $localhost = new Node('localhost');
        $localhost->setHostname('localhost');

        $this->shell->execute($command, $localhost, $deployment, true);
    }
}
