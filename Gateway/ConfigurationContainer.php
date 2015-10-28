<?php

namespace SmartInformationSystems\PaymentBundle\Gateway;

use SmartInformationSystems\PaymentBundle\Exception\Gateway\Exception;

/**
 * Настройки транспорта.
 *
 */
class ConfigurationContainer
{
    /**
     * Настройки.
     *
     * @var array
     */
    private $config = array();

    /**
     * Конструктор.
     *
     */
    public function __construct()
    {
    }

    /**
     * Установка конфига.
     *
     * @param array $config Конфиг
     *
     * @return ConfigurationContainer
     *
     * @throws Exception
     */
    public function setConfig(array $config)
    {
        if (empty($config)) {
            throw new Exception('Пустые настройки');
        }

        $this->config = $config;

        return $this;
    }

    /**
     * Возвращает настройки.
     *
     * @param string $name
     *
     * @return array
     *
     * @throws Exception
     */
    public function getGatewayParams($name)
    {
        if (empty($this->config['gateways'][$name])) {
            throw new Exception('Нет настроек платежного сервиса: ' . $name);
        }

        return $this->config['gateways'][$name];
    }

    /**
     * Возвращает путь.
     *
     * @param string $name
     *
     * @return string
     *
     * @throws Exception
     */
    public function getRoute($name)
    {
        if (empty($this->config['routes'][$name])) {
            throw new Exception('Нет пути: ' . $name);
        }

        return $this->config['routes'][$name];
    }
}