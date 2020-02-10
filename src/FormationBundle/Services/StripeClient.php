<?php

namespace FormationBundle\Services;

use FormationBundle\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use FormationBundle\Entity\FormationPayment;
use Psr\Log\LoggerInterface;
use Stripe\Charge;
use Stripe\Error\Base;
use Stripe\Stripe;

class StripeClient
{
    private $config;
    private $em;
    private $logger;
    public function __construct($secretKey, array $config, EntityManagerInterface $em, LoggerInterface $logger)
    {
        Stripe::setApiKey($secretKey);
        $this->config = $config;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function createCharge(Client $client, $token)
    {
        try {
            $charge = Charge::create([
                'amount' => $this->config['decimal'] ? $this->config['premium_amount'] * 10 : $this->config['premium_amount'],
                'currency' => $this->config['currency'],
                'description' => 'formation price',
                'source' => $token,
                'receipt_email' => $client->getEmail(),
            ]);
        } catch (Stripe\Error\Base $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a premium payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);
            throw $e;
        }

        $payment = new FormationPayment();
        $payment->setChargeId($charge->id);
        $payment->setClient($client);
        $this->em->persist($payment);
        $this->em->flush();
    }
}
