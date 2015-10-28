<?php

namespace SmartInformationSystems\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Платежи.
 *
 * @ORM\Entity(repositoryClass="SmartInformationSystems\PaymentBundle\Repository\PaymentRepository")
 * @ORM\Table(
 *   name="sis_payment",
 *   indexes={
 *   },
 *   uniqueConstraints={
 *   }
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Payment
{
    /**
     * Идентификатор.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Токен.
     *
     * @var string
     *
     * @ORM\Column(unique=true, length=32, nullable=false)
     */
    private $token;

    /**
     * Платежный сервис.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $gateway;

    /**
     * Дополнительные настройки.
     *
     * @var array
     *
     * @ORM\Column(name="gateway_options", type="json_array", nullable=true)
     */
    private $gatewayOptions;

    /**
     * Сумма платежа.
     *
     * @var double
     *
     * @ORM\Column(name="payment_sum", type="decimal", scale=2, nullable=false)
     */
    private $sum;

    /**
     * Идентификатор покупателя.
     *
     * @var string
     *
     * @ORM\Column(name="buyer_id", type="string", length=255, nullable=true)
     */
    private $buyerId;

    /**
     * Телефон покупателя.
     *
     * @var string
     *
     * @ORM\Column(name="buyer_phone", type="string", length=255, nullable=true)
     */
    private $buyerPhone;

    /**
     * Идентификатор заказа.
     *
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=255, nullable=true)
     */
    private $orderId;

    /**
     * Номер заказа.
     *
     * @var string
     *
     * @ORM\Column(name="order_number", type="string", length=255, nullable=true)
     */
    private $orderNumber;

    /**
     * Платеж подтвержден.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmed;

    /**
     * Данные от платежного сервиса.
     *
     * @var array
     *
     * @ORM\Column(name="gateway_data", type="json_array", nullable=true)
     */
    private $gatewayData;

    /**
     * Дата создания.
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    private $createdAt;

    /**
     * Дата изменения.
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    private $updatedAt;

    /**
     * Конструктор.
     *
     */
    public function __construct()
    {
        $this->confirmed = FALSE;
        $this->gatewayData = array();
    }

    /**
     * Возвращает идентификатор.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Устанавливает токен.
     *
     * @param string $token Токен
     *
     * @return Payment
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Возвращает токен.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Устанавливает платежный сервис.
     *
     * @param string $gateway
     *
     * @return Payment
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Возвращает платежный сервис.
     *
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Устанавливает идентификатор покупателя.
     *
     * @param string $buyerId
     *
     * @return Payment
     */
    public function setBuyerId($buyerId)
    {
        $this->buyerId = $buyerId;

        return $this;
    }

    /**
     * Возвращает идентификатор покупателя.
     *
     * @return string
     */
    public function getBuyerId()
    {
        return $this->buyerId;
    }

    /**
     * Устанавливает телефон покупателя.
     *
     * @param string $buyerPhone
     *
     * @return Payment
     */
    public function setBuyerPhone($buyerPhone)
    {
        $this->buyerPhone = $buyerPhone;

        return $this;
    }

    /**
     * Возвращает телефон покупателя.
     *
     * @return string
     */
    public function getBuyerPhone()
    {
        return $this->buyerPhone;
    }

    /**
     * Устанавливает идентификатор заказа.
     *
     * @param string $orderId
     *
     * @return Payment
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Возвращает идентификатор заказа.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Устанавливает номер заказа.
     *
     * @param string $orderNumber
     *
     * @return Payment
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Возвращает номер заказа.
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Устанавливает сумму.
     *
     * @param double $sum
     *
     * @return Payment
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Возвращает сумму.
     *
     * @return double
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Устанавливает подтверждение платежа.
     *
     * @param bool $confirmed
     *
     * @return Payment
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Возвращает подтверждение платежа.
     *
     * @return bool
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Устанавливает данные от платежного сервиса.
     *
     * @param array $gatewayData
     *
     * @return Payment
     */
    public function setGatewayData(array $gatewayData = array())
    {
        $this->gatewayData = $gatewayData;

        return $this;
    }

    /**
     * Возвращает данные от платежного сервиса.
     *
     * @return array
     */
    public function getGatewayData()
    {
        return $this->gatewayData;
    }

    /**
     * Устанавливает дату создания.
     *
     * @param \DateTime $createdAt Дата создания
     *
     * @return Payment
     */
    private function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Возвращает дату создания.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Устанавливает дату последнего изменения.
     *
     * @param \DateTime $updatedAt Дата последнего изменения
     *
     * @return Payment
     */
    private function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Возвращает дату последнего изменения.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Автоматическая установка даты создания.
     *
     * @ORM\PrePersist
     */
    public function prePersistHandler()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Автоматическая установка даты обновления.
     *
     * @ORM\PreUpdate
     */
    public function preUpdateHandler()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Устанавливает дополнительные параметры платежного сервиса.
     *
     * @param array $gatewayOptions
     *
     * @return Payment
     */
    public function setGatewayOptions($gatewayOptions)
    {
        $this->gatewayOptions = $gatewayOptions;

        return $this;
    }

    /**
     * Возвращает дополнительные параметры платежного сервиса.
     *
     * @return array
     */
    public function getGatewayOptions()
    {
        return $this->gatewayOptions;
    }
}
