<?php

namespace SmartInformationSystems\PaymentBundle\Repository;

use Doctrine\ORM\EntityRepository;

use SmartInformationSystems\PaymentBundle\Entity\Payment;

class PaymentRepository extends EntityRepository
{
    /**
     * @param string $token
     *
     * @return Payment
     */
    public function getByToken($token)
    {
        return $this->findOneBy(array(
            'token' => $token,
        ));
    }
}
