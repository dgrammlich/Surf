---------------------------
TYPO3\\Surf\\Task\\StopTask
---------------------------

.. php:namespace: TYPO3\\Surf\\Task

.. php:class:: StopTask

    A stop task that will stop execution inside a workflow (for testing purposes)

    .. php:method:: execute(Node $node, Application $application, Deployment $deployment, $options = array())

        Executes this task

        :type $node: Node
        :param $node:
        :type $application: Application
        :param $application:
        :type $deployment: Deployment
        :param $deployment:
        :type $options: array
        :param $options:
        :returns: void

    .. php:method:: simulate(Node $node, Application $application, Deployment $deployment, $options = array())

        Simulate this task

        :type $node: Node
        :param $node:
        :type $application: Application
        :param $application:
        :type $deployment: Deployment
        :param $deployment:
        :type $options: array
        :param $options:
        :returns: void
