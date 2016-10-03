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
 * Task for purging in Varnish, should be used for Varnish 2.x
 *
 * It takes the following options:
 *
 * * secretFile (optional) - Path to the secret file, defaults to "/etc/varnish/secret".
 * * purgeUrl (optional) - URL (pattern) to purge, defaults to ".".
 * * varnishadm (optional) - Path to the varnishadm utility, defaults to "/usr/bin/varnishadm".
 *
 * Example:
 *  $workflow
 *      ->setTaskOptions('TYPO3\Surf\Task\VarnishPurgeTask', [
 *              'secretFile' => '/etc/varnish/secret',
 *              'purgeUrl' => '.',
 *              'varnishadm' => '/usr/bin/varnishadm'
 *          ]
 *      );
 */
class VarnishPurgeTask extends \TYPO3\Surf\Domain\Model\Task implements \TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface
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
        $secretFile = (isset($options['secretFile']) ? $options['secretFile'] : '/etc/varnish/secret');
        $purgeUrl = (isset($options['purgeUrl']) ? $options['purgeUrl'] : '.');
        $varnishadm = (isset($options['varnishadm']) ? $options['varnishadm'] : '/usr/bin/varnishadm');

        $this->shell->executeOrSimulate($varnishadm . ' -S ' . $secretFile . ' -T 127.0.0.1:6082 url.purge ' . $purgeUrl, $node, $deployment);
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
        $secretFile = (isset($options['secretFile']) ? $options['secretFile'] : '/etc/varnish/secret');
        $varnishadm = (isset($options['varnishadm']) ? $options['varnishadm'] : '/usr/bin/varnishadm');

        $this->shell->executeOrSimulate($varnishadm . ' -S ' . $secretFile . ' -T 127.0.0.1:6082 status', $node, $deployment);
    }
}
