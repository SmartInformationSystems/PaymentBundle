<?php

namespace SmartInformationSystems\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SmartInformationSystems\PaymentBundle\Exception\Gateway\AuthorizationException;
use SmartInformationSystems\PaymentBundle\Gateway\AbstractGateway;

class YandexKassaBackendController extends BackendController
{
    const GATEWAY_NAME = 'yandex_kassa';

    /**
     * Проверка платежа.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function checkAction(Request $request)
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

            $this->logRequest($payment, $request->request->get('action'), $request->request->all());

            $params['invoiceId'] = $request->request->get('invoiceId');
            $params['orderSumAmount'] = $request->request->get('orderSumCurrencyPaycash');

            $this->checkSign($gateway, $request);

            $params['code'] = '0';

        } catch (AuthorizationException $e) {
            $this->logException($e);
            $params['code'] = '1';
            $params['message'] = $e->getMessage();

        } catch (\Exception $e) {
            $this->logException($e);
            $params['code'] = $e->getCode() ? $e->getCode() : '200';
            $params['message'] = 'Internal Service Error';
            $params['techMessage'] = $e->getMessage();
        }

        return $this->makeResponse($gateway, $request->request->get('action'), $params);
    }

    /**
     * Подтерждение платежа.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function avisoAction(Request $request)
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

            $this->logRequest($payment, $request->request->get('action'), $request->request->all());

            $params['invoiceId'] = $request->request->get('invoiceId');
            $params['orderSumAmount'] = $request->request->get('orderSumCurrencyPaycash');

            $this->checkSign($gateway, $request);

            $this->confirmPayment($payment, $request->request->all());

            $params['code'] = '0';

        } catch (AuthorizationException $e) {
            $this->logException($e);
            $params['code'] = '1';
            $params['message'] = $e->getMessage();

        } catch (\Exception $e) {
            $this->logException($e);
            $params['code'] = $e->getCode() ? $e->getCode() : '200';
            $params['message'] = 'Internal Service Error';
            $params['techMessage'] = $e->getMessage();
        }

        return $this->makeResponse($gateway, $request->request->get('action'), $params);
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
            if (!($paymentToken = $request->query->get('paymentId'))) {
                throw new \Exception('Empty paymentId');
            }
            if (!($payment = $this->getPaymentRepository()->getByToken($paymentToken))) {
                throw new \Exception('Payment not found: ' . $paymentToken);
            }

            $this->logRequest($payment, $request->query->get('action'), $request->query->all());

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
            if (!($paymentToken = $request->query->get('paymentId'))) {
                throw new \Exception('Empty paymentId');
            }
            if (!($payment = $this->getPaymentRepository()->getByToken($paymentToken))) {
                throw new \Exception('Payment not found: ' . $paymentToken);
            }

            $this->logRequest($payment, $request->query->get('action'), $request->query->all());

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
            $request->request->get('action')
            . ';' . $request->request->get('orderSumAmount')
            . ';' . $request->request->get('orderSumCurrencyPaycash')
            . ';' . $request->request->get('orderSumBankPaycash')
            . ';' . $request->request->get('shopId')
            . ';' . $request->request->get('invoiceId')
            . ';' . $request->request->get('customerNumber')
            . ';' . $gateway->getParam('shop_password')
        );

        if (strtoupper($sign) != $request->request->get('md5')) {
            throw new AuthorizationException('Ошибка подписи', 1);
        }
    }

    /**
     * Формирует xml-ответ.
     *
     * @param AbstractGateway $gateway
     * @param array $params
     *
     * @return Response
     */
    private function makeResponse(AbstractGateway $gateway = NULL, $action, $params) {
        $response = new Response();
        $response->headers->set('Content-Type', 'xml');
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $action . 'Response/>');
        $xml['performedDatetime'] = date('c');

        if ($gateway) {
            $xml['shopId'] = $gateway->getParam('shop_id');
        }

        foreach ($params as $key => $value) {
            $xml[$key] = $value;
        }

        $data = $xml->asXML();

        $this->logResponse($data);

        $response->setContent($data);
        return $response;
    }
}
