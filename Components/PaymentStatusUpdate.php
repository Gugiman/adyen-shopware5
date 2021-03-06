<?php

declare(strict_types=1);

namespace AdyenPayment\Components;

use AdyenPayment\Models\Event;
use Psr\Log\LoggerInterface;
use Shopware\Components\ContainerAwareEventManager;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class PaymentStatusUpdate
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ContainerAwareEventManager
     */
    private $eventManager;

    /**
     * PaymentStatusUpdate constructor.
     * @param ModelManager $modelManager
     * @param ContainerAwareEventManager $eventManager
     */
    public function __construct(
        ModelManager $modelManager,
        ContainerAwareEventManager $eventManager
    ) {
        $this->modelManager = $modelManager;
        $this->eventManager = $eventManager;
    }

    /**
     * @param Order $order
     * @param int $statusId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function updateOrderStatus(Order $order, int $statusId)
    {
        $orderStatus = $this->modelManager->find(Status::class, $statusId);

        if ($this->logger) {
            $this->logger->debug('Update order status', [
                'number' => $order->getNumber(),
                'oldStatus' => $order->getOrderStatus()->getName(),
                'newStatus' => $orderStatus->getName()
            ]);
        }

        $this->eventManager->notify(
            Event::ORDER_STATUS_CHANGED,
            [
                'order' => $order,
                'newStatus' => $orderStatus
            ]
        );

        $order->setOrderStatus($orderStatus);
        $this->modelManager->persist($order);
        $this->modelManager->flush();
    }

    /**
     * @param Order $order
     * @param int $statusId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function updatePaymentStatus(Order $order, int $statusId)
    {
        $paymentStatus = $this->modelManager->find(Status::class, $statusId);

        if ($this->logger) {
            $this->logger->debug('Update order payment status', [
                'number' => $order->getNumber(),
                'oldStatus' => $order->getPaymentStatus()->getName(),
                'newStatus' => $paymentStatus->getName()
            ]);
        }

        $this->eventManager->notify(
            Event::ORDER_PAYMENT_STATUS_CHANGED,
            [
                'order' => $order,
                'newStatus' => $paymentStatus
            ]
        );

        $order->setPaymentStatus($paymentStatus);
        $this->modelManager->persist($order);
        $this->modelManager->flush();
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}
