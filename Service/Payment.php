<?php

namespace SmartInformationSystems\PaymentBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

use SmartInformationSystems\PaymentBundle\Gateway\ConfigurationContainer;
use SmartInformationSystems\PaymentBundle\PaymentForm\AbstractPaymentForm;
use SmartInformationSystems\PaymentBundle\Gateway\YandexKassaGateway;
use SmartInformationSystems\PaymentBundle\Gateway\PaymasterGateway;

use SmartInformationSystems\PaymentBundle\Exception\Gateway\Exception;
use SmartInformationSystems\PaymentBundle\Exception\Gateway\UnknownException;

/**
 * Сервис для работы с платежными шлюзами.
 *
 */
class Payment
{
    /**
     * @var ConfigurationContainer
     */
    private $configuration;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Конструктор.
     *
     * @param ConfigurationContainer $configuration
     * @param Session $session
     * @param EntityManager $em
     */
    public function __construct(ConfigurationContainer $configuration, Session $session, EntityManager $em)
    {
        $this->configuration = $configuration;
        $this->session = $session;
        $this->em = $em;
    }

    /**
     * Возвращает интерфейс работы с платежным сервисом.
     *
     * @param string $name
     *
     * @return YandexKassaGateway
     *
     * @throws Exception
     */
    public function getGateway($name)
    {
        switch ($name) {
            case 'yandex_kassa':
                return new YandexKassaGateway(
                    $this->configuration->getGatewayParams($name),
                    $this->session,
                    $this->em
                );
            case 'paymaster':
                return new PaymasterGateway(
                    $this->configuration->getGatewayParams($name),
                    $this->session,
                    $this->em
                );
            default:
                throw new UnknownException;
        }
    }

    /**
     * Возвращает платежную форму по токену.
     *
     * @param string $token
     *
     * @return AbstractPaymentForm
     *
     * @throws Exception
     */
    public function getPaymentForm($token)
    {
        if (!($payment = $this->em->getRepository('SmartInformationSystemsPaymentBundle:Payment')->getByToken($token))) {
            throw new Exception('Платеж не найден: ' . $token);
        }

        if ($payment->getConfirmed()) {
            throw new Exception('Платеж уже подтвержден: ' . $token);
        }

        $gateway = self::getGateway($payment->getGateway())->setOptions($payment->getGatewayOptions());

        return $gateway->getPaymentForm($payment);
    }

    public function getSuccessPaymentRoute()
    {
        return $this->configuration->getRoute('success');
    }

    public function getFailPaymentRoute()
    {
        return $this->configuration->getRoute('fail');
    }
}
