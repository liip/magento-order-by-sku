<?php

namespace Liip\OrderBySku\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Post extends Action
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CartInterface
     */
    private $cart;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param CartInterface $cart
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        CartInterface $cart,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $sku = $this->getRequest()->getParam('sku');
            $qty = $this->getRequest()->getParam('qty');
            if (!$sku || !$qty) {
                throw new \InvalidArgumentException(__('Invalid arguments specified.'));
            }

            $product = $this->productRepository->get($sku);
            $this->cart->addProduct($product, ['product' => $product->getId(), 'qty' => $qty]);
            $this->cart->saveQuote();
            $message = __(
                'You added %1 to your shopping cart.',
                $product->getName()
            );
            $this->messageManager->addSuccessMessage($message);
        } catch (NoSuchEntityException $noSuchEntityException) {
            $this->messageManager->addWarningMessage($noSuchEntityException->getMessage());
        } catch (\InvalidArgumentException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Error happened while processing request.'));
            $this->logger->error($exception->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
