<?php

namespace SmartInformationSystems\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use SmartInformationSystems\PaymentBundle\Event\PaymentConfirmEvent;
use SmartInformationSystems\PaymentBundle\PaymentEvents;
use SmartInformationSystems\PaymentBundle\Entity\Payment;
use SmartInformationSystems\PaymentBundle\Entity\PaymentLog;

class BackendController extends Controller
{
    const GATEWAY_NAME = 'unknown';

    /**
     * @var PaymentLog
     */
    private $log;

    /**
     * Логирование запроса.
     *
     * @param Payment $payment
     * @param string $type
     * @param array $data
     *
     * @return void
     */
    protected function logRequest(Payment $payment, $type, array $data)
    {
        $this->log = new PaymentLog();
        $this->log->setPayment($payment);
        $this->log->setType($type);
        $this->log->setGateway(static::GATEWAY_NAME);
        $this->log->setRequestData($data);

        $this->getDoctrine()->getManager()->persist($this->log);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * Логирование ошибки.
     *
     * @param \Exception $e
     */
    protected function logException(\Exception $e)
    {
        if (empty($this->log)) {
            $this->log = new PaymentLog();
            $this->log->setGateway(static::GATEWAY_NAME);
        }

        $this->log->setErrorData($e->__toString());

        $this->getDoctrine()->getManager()->persist($this->log);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * Логирование ошибки.
     *
     * @param string $data
     */
    protected function logResponse($data)
    {
        if (empty($this->log)) {
            $this->log = new PaymentLog();
            $this->log->setGateway(static::GATEWAY_NAME);
        }

        $this->log->setResponseData($data);

        $this->getDoctrine()->getManager()->persist($this->log);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * Подтверждение платежа.
     *
     * @param Payment $payment
     * @param array $data
     *
     * @return void
     */
    protected function confirmPayment(Payment $payment, array $data = array())
    {
        $needEvent = !$payment->getConfirmed();

        $payment->setConfirmed(TRUE);
        $payment->setGatewayData(array_merge(
            $payment->getGatewayData(),
            $data
        ));
        $this->getDoctrine()->getManager()->persist($payment);
        $this->getDoctrine()->getManager()->flush();

        if ($needEvent) {
            $this->get('event_dispatcher')->dispatch(
                PaymentEvents::PAYMENT_CONFIRM,
                new PaymentConfirmEvent($payment)
            );
        }
    }

    protected function successRedirect(Payment $payment)
    {
        return $this->redirectToRoute(
            $this->get('sis_payment')->getSuccessPaymentRoute(),
            array(
                'order' => $payment->getOrderId(),
            )
        );
    }

    protected function failRedirect(Payment $payment)
    {
        return $this->redirectToRoute(
            $this->get('sis_payment')->getFailPaymentRoute(),
            array(
                'order' => $payment->getOrderId(),
            )
        );
    }
    protected function getPaymentRepository()
    {
        static $paymentRepository;

        if (!$paymentRepository) {
            $paymentRepository = $this->getDoctrine()->getRepository('SmartInformationSystemsPaymentBundle:Payment');
        }

        return $paymentRepository;
    }
}
