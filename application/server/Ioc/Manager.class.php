<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Armand
 * Date: 12/07/13
 * Time: 17:57
 * To change this template use File | Settings | File Templates.
 */

namespace Ioc;


class Manager
{
    /**
     * @var Container
     */
    private static $_clientIocContairnerInstance;

    /**
     * @var string
     */
    private static $_clientIocContaienerConfigurationFilePath = '/ioc-client.xml';

    /**
     * @var Container
     */
    private static $_serverIocContairnerInstance;

    /**
     * @var string
     */
    private static $_serverIocContaienerConfigurationFilePath = '/ioc-client.xml';


    /**
     * @throws \Exception
     */
    public static function initClientContainerInstance()
    {
        if (null !== self::$_clientIocContairnerInstance) {
            throw new \Exception('Impossible d\'initialiser deux fois le container IOC client.');
        }

        self::$_clientIocContairnerInstance = new Container(CONF_PATH . self::$_clientIocContaienerConfigurationFilePath);
    }

    /**
     * @throws \Exception
     */
    public static function initServerContainerInstance()
    {
        if (null !== self::$_serverIocContairnerInstance) {
            throw new \Exception('Impossible d\'initialiser deux fois le container IOC server.');
        }

        self::$_serverIocContairnerInstance = new Container(CONF_PATH . self::$_serverIocContaienerConfigurationFilePath);
    }


    /**
     * @return Container
     */
    public static function getClientContainerInstance()
    {
        return self::$_clientIocContairnerInstance;
    }

    /**
     * @return Container
     */
    public static function getServerContainerInstance()
    {
        return self::$_clientIocContairnerInstance;
    }

}