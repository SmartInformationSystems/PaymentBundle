<?php

namespace SmartInformationSystems\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontendController extends Controller
{
    /**
     * Платежная форма.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paymentFormAction(Request $request)
    {
        if (!($token = $request->query->get('token'))) {
            throw $this->createNotFoundException('Нет токена платежной формы');
        }

        return $this->render(
            'SmartInformationSystemsPaymentBundle:frontend:form.html.twig',
            array(
                'form' => $this->get('sis_payment')->getPaymentForm($token)
            )
        );
    }
}
