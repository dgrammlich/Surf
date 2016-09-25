---------------------------------
TYPO3\\Surf\\Task\\LocalShellTask
---------------------------------

.. php:namespace: TYPO3\\Surf\\Task

.. php:class:: LocalShellTask

    A shell task for local packaging

    .. php:method:: execute(Node $node, Application $application, Deployment $deployment, $options = array())

        Executes this task

        Options:
        command: The command to execute rollbackCommand: The command to execute as
        a rollback (optional)

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

    .. php:method:: rollback(Node $node, Application $application, Deployment $deployment, $options = array())

        Rollback this task

        :type $node: Node
        :param $node:
        :type $application: Application
        :param $application:
        :type $deployment: Deployment
        :param $deployment:
        :type $options: array
        :param $options:
        :returns: void
