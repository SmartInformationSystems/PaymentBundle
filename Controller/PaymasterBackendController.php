<?php

namespace SmartInformationSystems\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SmartInformationSystems\PaymentBundle\Exception\Gateway\AuthorizationException;
use SmartInformationSystems\PaymentBundle\Gateway\AbstractGateway;

class PaymasterBackendController extends BackendController
{
    const GATEWAY_NAME = 'paymaster';

    /**
     * Проверка платежа.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function checkAction(Request $request)
    {
        try {
            if (!($paymentToken = $request->request->get('paymentId'))) {
                throw new \Exception('Empty paymentId');
            }
            if (!($payment = $this->getPaymentRepository()->getByToken($paymentToken))) {
                throw new \Exception('Payment not found: ' . $paymentToken);
            }

            $this->logRequest($payment, 'Invoice confirmation', $request->request->all());

            if ($payment->getConfirmed()) {
                $response = 'already confirmed';
            } else {
                $response = 'YES';
            }

            $this->logResponse($response);

            return new Response($response);

        } catch (AuthorizationException $e) {
            $this->logException($e);
            return new Response($e->getMessage());

        } catch (\Exception $e) {
            $this->logException($e);
            return new Response('Internal Service Error');
        }
    }

    /**
     * Подтерждение платежа.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function confirmAction(Request $request)
    {
        $params = array();
        $gateway = NULL;

        try {
            $gateway = $this->get('sis_payment')->getGateway(static::GATEWAY_NAME);

            if (!($paymentToken = $request->request->get('paymentId'))) {
                throw new \Exception('Empty paymentId');
            }
            if (!($payment = $this->getPaymentRepository()->getByToken($paymentToken))) {
                throw new \Exception('Payment not found: ' . $paymentToken);
            }

            $this->logRequest($payment, 'Payment notification', $request->request->all());

            $this->checkSign($gateway, $request);

            $this->confirmPayment($payment, $request->request->all());

            $response = 'success';

        } catch (AuthorizationException $e) {
            $this->logException($e);
            $response = $e->getMessage();

        } catch (\Exception $e) {
            $this->logException($e);
            $response = 'Internal Service Error';
        }

        return new Response($response);
    }

    /**
     * Успешный платеж.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function successAction(Request $request)
    {
        try {
            if (!($paymentToken = $request->request->get('paymentId'))) {
                throw new \Exception('Empty paymentId');
            }
            if (!($payment = $this->getPaymentRepository()->getByToken($paymentToken))) {
                throw new \Exception('Payment not found: ' . $paymentToken);
            }

            $this->logRequest($payment, 'Success redirect', $request->request->all());

            if (!$payment->getConfirmed()) {
                throw new \Exception('Payment not confirmed: ' . $paymentToken);
            }

            return $this->successRedirect($payment);

        } catch (\Exception $e) {
            $this->logException($e);
        }

        return $this->redirect('/');
    }

    /**
     * Неуспешный платеж.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function failAction(Request $request)
    {
        try {
            if (!($paymentToken = $request->request->get('paymentId'))) {
                throw new \Exception('Empty paymentId');
            }
            if (!($payment = $this->getPaymentRepository()->getByToken($paymentToken))) {
                throw new \Exception('Payment not found: ' . $paymentToken);
            }

            $this->logRequest($payment, 'Failure redirect', $request->request->all());

            return $this->failRedirect($payment);

        } catch (\Exception $e) {
            $this->logException($e);
        }

        return $this->redirect('/');
    }

    /**
     * Проверка подписи запроса.
     *
     * @param AbstractGateway $gateway
     * @param Request $request
     *
     * @throws AuthorizationException
     */
    private function checkSign(AbstractGateway $gateway, Request $request)
    {
        $sign = md5(
            $request->request->get('LMI_MERCHANT_ID')
                . ';' . $request->request->get('LMI_PAYMENT_NO')
                . ';' . $request->request->get('LMI_SYS_PAYMENT_ID')
                . ';' . $request->request->get('LMI_SYS_PAYMENT_DATE')
                . ';' . $request->request->get('LMI_PAYMENT_AMOUNT')
                . ';' . $request->request->get('LMI_CURRENCY')
                . ';' . $request->request->get('LMI_PAID_AMOUNT')
                . ';' . $request->request->get('LMI_PAID_CURRENCY')
                . ';' . $request->request->get('LMI_PAYMENT_SYSTEM')
                . ';' . $request->request->get('LMI_SIM_MODE')
                . ';' . $gateway->getParam('shop_password')
            , TRUE
        );

        if (base64_encode($sign) != $request->request->get('LMI_HASH')) {
            throw new AuthorizationException('Wrong sign');
        }
    }
}
