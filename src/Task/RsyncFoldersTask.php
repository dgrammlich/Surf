<?php
namespace TYPO3\Surf\Task;

/*                                                                        *
 * This script belongs to the TYPO3 project "TYPO3 Surf"                  *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Exception\InvalidConfigurationException;

/**
 * A task to synchronize folders from the machine that runs Surf to a remote host by using Rsync.
 *
 * It takes the following options:
 *
 * * folders - An array with folders to synchronize. The key holds the source folder, the value holds the target folder.
 *   The target folder must have an absolute path.
 * * username (optional) - The username to log in on the remote host.
 * * ignoreErrors (optional) - If true, ignore errors during execution. Default is true.
 * * logOutput (optional) - If true, output the log. Default is false.
 *
 * Example:
 *  $workflow
 *      ->setTaskOptions('TYPO3\Surf\Task\RsyncFoldersTask', [
 *              'folders' => [
 *                  'uploads/spaceship' => '/var/www/outerspace/uploads/spaceship',
 *                  'uploads/freighter' => '/var/www/outerspace/uploads/freighter',
 *                  '/tmp/outerspace/lonely_planet' => '/var/www/outerspace/uploads/lonely_planet'
 *              ]
 *          ]
 *      );
 */
class RsyncFoldersTask extends \TYPO3\Surf\Domain\Model\Task implements \TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface
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
        if (!isset($options['folders'])) {
            return;
        }
        $folders = $options['folders'];
        if (!is_array($folders)) {
            $folders = array($folders);
        }
        $replacePaths = array(
            '{deploymentPath}' => escapeshellarg($application->getDeploymentPath()),
            '{sharedPath}' => escapeshellarg($application->getSharedPath()),
            '{releasePath}' => escapeshellarg($deployment->getApplicationReleasePath($application)),
            '{currentPath}' => escapeshellarg($application->getReleasesPath() . '/current'),
            '{previousPath}' => escapeshellarg($application->getReleasesPath() . '/previous')
        );

        $commands = array();

        $username = isset($options['username']) ? $options['username'] . '@' : '';
        $hostname = $node->getHostname();
        $port = $node->hasOption('port') ? '-P ' . escapeshellarg($node->getOption('port')) : '';

        foreach ($folders as $folderPair) {
            if (!is_array($folderPair) || count($folderPair) !== 2) {
                throw new InvalidConfigurationException('Each rsync folder definition must be an array of exactly two folders', 1405599056);
            }
            $sourceFolder = rtrim(str_replace(array_keys($replacePaths), $replacePaths, $folderPair[0]), '/') . '/';
            $targetFolder = rtrim(str_replace(array_keys($replacePaths), $replacePaths, $folderPair[1]), '/') . '/';
            $commands[] = "rsync -avz --delete -e ssh {$sourceFolder} {$username}{$hostname}:{$targetFolder}";
        }

        $ignoreErrors = isset($options['ignoreErrors']) && $options['ignoreErrors'] === true;
        $logOutput = !(isset($options['logOutput']) && $options['logOutput'] === false);

        $localhost = new Node('localhost');
        $localhost->setHostname('localhost');

        $this->shell->executeOrSimulate($commands, $localhost, $deployment, $ignoreErrors, $logOutput);
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
