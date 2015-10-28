<?php

namespace SmartInformationSystems\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Логи запросов по платежу.
 *
 * @ORM\Entity(repositoryClass="SmartInformationSystems\PaymentBundle\Repository\PaymentLogRepository")
 * @ORM\Table(
 *   name="sis_payment_log",
 *   indexes={
 *   },
 *   uniqueConstraints={
 *   }
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class PaymentLog
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
     * Платеж.
     *
     * @var Payment
     *
     * @ORM\ManyToOne(targetEntity="Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id", nullable=true)
     */
    private $payment;

    /**
     * Платежный сервис.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $gateway;

    /**
     * Тип запроса.
     *
     * @var array
     *
     * @ORM\Column(name="request_type", type="string", nullable=true)
     */
    private $type;

    /**
     * Данные от платежного сервиса.
     *
     * @var array
     *
     * @ORM\Column(name="request_data", type="json_array", nullable=true)
     */
    private $requestData;

    /**
     * Данные ответа.
     *
     * @var string
     *
     * @ORM\Column(name="response_data", type="text", nullable=true)
     */
    private $responseData;

    /**
     * Ошибка.
     *
     * @var string
     *
     * @ORM\Column(name="error_data", type="text", nullable=true)
     */
    private $errorData;

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
        $this->data = array();
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
     * Устанавливает платежный сервис.
     *
     * @param string $gateway
     *
     * @return PaymentLog
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
     * Устанавливает платеж.
     *
     * @param Payment $payment
     *
     * @return PaymentLog
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Возвращает платеж.
     *
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Устанавливает тип запроса.
     *
     * @param string $type
     *
     * @return PaymentLog
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Возвращает тип запроса.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Устанавливает данные от платежного сервиса.
     *
     * @param array $data
     *
     * @return Payment
     */
    public function setRequestData(array $data = array())
    {
        $this->requestData = $data;

        return $this;
    }

    /**
     * Возвращает данные от платежного сервиса.
     *
     * @return bool
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * Устанавливает данные ответа.
     *
     * @param string $data
     *
     * @return Payment
     */
    public function setResponseData($data)
    {
        $this->responseData = $data;

        return $this;
    }

    /**
     * Возвращает данные ответа.
     *
     * @return bool
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * Устанавливает данные ошибки.
     *
     * @param string $data
     *
     * @return Payment
     */
    public function setErrorData($data)
    {
        $this->errorData = $data;

        return $this;
    }

    /**
     * Возвращает данные ошибки.
     *
     * @return bool
     */
    public function getErrorData()
    {
        return $this->errorData;
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
}
