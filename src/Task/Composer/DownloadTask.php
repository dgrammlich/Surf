<?php
namespace TYPO3\Surf\Task\Composer;

/*                                                                        *
 * This script belongs to the TYPO3 project "TYPO3 Surf"                  *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;

/**
 * Downloads Composer into the current releasePath.
 *
 * It takes the following options:
 *
 * * composerDownloadCommand (optional) - The command that should be used to download Composer instead of the regular command.
 *
 * Example:
 *  $workflow
 *      ->setTaskOptions('TYPO3\Surf\Task\Composer\DownloadTask', [
 *              'composerDownloadCommand' => 'curl -s https://getcomposer.org/installer | php'
 *          ]
 *      );
 */
class DownloadTask extends \TYPO3\Surf\Domain\Model\Task implements \TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface
{
    use \TYPO3\Surf\Domain\Service\ShellCommandServiceAwareTrait;

    /**
     * Execute this task
     *
     * @param \TYPO3\Surf\Domain\Model\Node $node
     * @param \TYPO3\Surf\Domain\Model\Application $application
     * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
     * @param array $options
     * @throws \TYPO3\Surf\Exception\TaskExecutionException
     * @return void
     */
    public function execute(Node $node, Application $application, Deployment $deployment, array $options = array())
    {
        $applicationReleasePath = $deployment->getApplicationReleasePath($application);

        if (isset($options['composerDownloadCommand'])) {
            $composerDownloadCommand = $options['composerDownloadCommand'];
        } else {
            $composerDownloadCommand = 'curl -s https://getcomposer.org/installer | php';
        }

        $command = sprintf('cd %s && %s', escapeshellarg($applicationReleasePath), $composerDownloadCommand);
        $this->shell->executeOrSimulate($command, $node, $deployment);
    }
}
