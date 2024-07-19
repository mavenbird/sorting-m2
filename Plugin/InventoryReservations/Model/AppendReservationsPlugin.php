<?php
/**
 * Mavenbird Technologies Private Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mavenbird.com/Mavenbird-Module-License.txt
 *
 * =================================================================
 *
 * @category   Mavenbird
 * @package    Mavenbird_Sorting
 * @author     Mavenbird Team
 * @copyright  Copyright (c) 2018-2024 Mavenbird Technologies Private Limited ( http://mavenbird.com )
 * @license    http://mavenbird.com/Mavenbird-Module-License.txt
 */

declare(strict_types=1);

namespace Mavenbird\Sorting\Plugin\InventoryReservations\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Validation\ValidationException;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class AppendReservationsPlugin
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Construct
     *
     * @param SerializerInterface $serializer
     * @param OrderRepositoryInterface $orderRepository
     * @param ObjectManagerInterface $objectManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        SerializerInterface $serializer,
        OrderRepositoryInterface $orderRepository,
        ObjectManagerInterface $objectManager,
        LoggerInterface $logger
    ) {
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    /**
     * BeforeExecute
     *
     * @param [type] $subject
     * @param array $reservations
     * @return void
     */
    public function beforeExecute($subject, array $reservations)
    {
        $parentReservations = [];
        if ($reservations) {
            $reservation = reset($reservations);
            try {
                $metadata = $this->unserialize($reservation->getMetadata());
                if (isset($metadata['object_type'])
                    && isset($metadata['object_id'])
                    && $metadata['object_type'] == 'order'
                ) {
                    $order = $this->orderRepository->get($metadata['object_id']);
                    foreach ($reservations as $reservation) {
                        if ($newReservation = $this->createParentReservation($order, $reservation)) {
                            $parentReservations[] = $newReservation;
                        }
                    }
                }
            } catch (\InvalidArgumentException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return [array_merge($reservations, $parentReservations)];
    }

    /**
     * CreateParentReservation
     *
     * @param [type] $order
     * @param [type] $reservation
     * @return void
     */
    private function createParentReservation($order, $reservation)
    {
        $parentReservation = false;

        foreach ($order->getItems() as $orderItem) {
            if ($orderItem->getParentItemId()
                && $orderItem->getParentItem()->getProductType() == Configurable::TYPE_CODE
                && $orderItem->getSku() == $reservation->getSku()
            ) {
                try {
                    $parentReservation = $this->getReservationBuilder()
                        ->setSku($orderItem->getParentItem()->getProduct()->getSku())
                        ->setQuantity((float)$reservation->getQuantity())
                        ->setStockId($reservation->getStockId())
                        ->setMetadata($reservation->getMetadata())
                        ->build();
                    break;
                } catch (ValidationException $e) {
                    $this->logger->error($e->getMessage());
                    $parentReservation = false;
                    continue;
                }
            }
        }

        return $parentReservation;
    }

    /**
     * Unserialize
     *
     * @param [type] $data
     * @return void
     */
    private function unserialize($data)
    {
        return $this->serializer->unserialize($data);
    }

    /**
     * GetReservationBuilder
     *
     * @return void
     */
    private function getReservationBuilder()
    {
        return $this->objectManager->get(
            \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface::class
        );
    }
}
