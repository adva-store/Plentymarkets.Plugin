<?php

namespace Advastore\Services\Order;

use Advastore\Helper\OrderHelper;
use Advastore\Services\Rest\WebserviceMethods;
use Exception;
use Plenty\Modules\Document\Contracts\DocumentRepositoryContract;
use Plenty\Modules\Document\Models\Document;
use Plenty\Modules\Order\Models\Order;
use Plenty\Plugin\Log\Loggable;

/**
 * Class OrderDocumentsService
 *
 * Service to handle order documents and their sending to Advastore.
 */
class OrderDocumentsService
{
    use Loggable;

    /**
     * OrderDocumentsService constructor.
     *
     * @param DocumentRepositoryContract $documentRepository The document repository instance.
     */
    public function __construct(
        private DocumentRepositoryContract $documentRepository,
        private WebserviceMethods $webserviceMethods
    ){}

    /**
     * Send the latest invoice for a given order.
     *
     * @param Order $order
     * @return bool Returns true if the invoice was sent successfully, otherwise false.
     * @throws Exception
     */
    public function sendLatestInvoice(Order $order): mixed
    {
        $document = $this->getDocument($order->id,Document::INVOICE);
        $externalOrderId = OrderHelper::getExternalOrderId($order->toArray());

        if ($document && $externalOrderId && !empty($document->content))
        {
            return $this->webserviceMethods->sendInvoiceDocument(
                $externalOrderId,
                $document->content
            );
        }

        $this
            ->getLogger('event:send-invoice')
            ->addReference('orderId',$order->id)
            ->error('No document found!');

        return false;
    }

    /**
     * Send the latest delivery note for a given order.
     *
     * @param Order $order
     * @return bool Returns true if the delivery note was sent successfully, otherwise false.
     * @throws Exception
     */
    public function sendLatestDeliveryNote(Order $order): mixed
    {
        $document = $this->getDocument($order->id,Document::DELIVERY_NOTE);
        $externalOrderId = OrderHelper::getExternalOrderId($order->toArray());

        if ($document && $externalOrderId && !empty($document->content))
        {
            return $this->webserviceMethods->sendDeliveryNoteDocument(
                $externalOrderId,
                $document->content
            );
        }

        throw new Exception('No document found!');
    }

    /**
     * Send the latest return note for a given order.
     *
     * @param Order $order
     * @return bool Returns true if the delivery note was sent successfully, otherwise false.
     * @throws Exception
     */
    public function sendReturnRecipientNote(Order $order): mixed
    {
        $document = $this->getDocument($order->id,Document::RETURN_NOTE);
        $externalOrderId = OrderHelper::getExternalOrderId($order->toArray());

        if ($document && $externalOrderId && !empty($document->content))
        {
            return $this->webserviceMethods->sendReturnReceipt(
                $externalOrderId,
                $document->content
            );
        }

        throw new Exception('No document found!');
    }

    /**
     * Send the latest shipping label for a given order.
     *
     * @param Order $order
     * @return bool Returns true if the shipping label was sent successfully, otherwise false.
     * @throws Exception
     */
    public function sendReturnLabel(Order $order): bool
    {
        $document = $this->getDocument($order->id,Document::RETURNS_LABEL);
        $externalOrderId = OrderHelper::getExternalOrderId($order->toArray());

        if ($document && $externalOrderId && !empty($document->content))
        {
            return $this->webserviceMethods->sendReturnLabelDocument(
                $externalOrderId,
                $document->content
            );
        }

        throw new Exception('No document found!');
    }

    /**
     * Get the current order document of a specific type for the given order ID.
     *
     * @param int $orderId The ID of the order for which to retrieve the document.
     * @param string $documentType The type of the document to retrieve
     * @return Document|bool
     */
    private function getDocument(int $orderId,string $documentType): Document|bool
    {
        try {
            return $this->documentRepository->findRecentOrderDocument($orderId, $documentType,true);
        }
        /** @noinspection PhpUnusedLocalVariableInspection */ catch (Exception $e) {
            return false;
        }
    }
}

