----------------------------------------
TYPO3\\Surf\\Task\\SourceforgeUploadTask
----------------------------------------

.. php:namespace: TYPO3\\Surf\\Task

.. php:class:: SourceforgeUploadTask

    Task for uploading to sourceforge

    .. php:method:: execute(Node $node, Application $application, Deployment $deployment, $options = array())

        Execute this task

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

    .. php:method:: checkOptionsForValidity($options)

        Check if all required options are given

        :type $options: array
        :param $options:
        :returns: void
