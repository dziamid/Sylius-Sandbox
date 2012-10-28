<?php

namespace Sylius\Sandbox\Bundle\CoreBundle\Process\Step;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep;
use Sylius\Component\Addressing\Model\AddressInterface;

/**
 * Delivery step.
 * Allows user to select delivery method for order.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DeliveryStep extends ContainerAwareStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $address = $context->getStorage()->get('delivery.address');
        $form = $this->createAddressForm($address);

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Process/Checkout/Step:delivery.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $context->getRequest();
        $form = $this->createAddressForm();

        if ($request->isMethod('POST') && $form->bindRequest($request)->isValid()) {
            $context->getStorage()->set('delivery.address', $form->getData());

            return $context->complete();
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Process/Checkout/Step:delivery.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    private function createAddressForm(AddressInterface $address = null)
    {
        return $this->container->get('form.factory')->create('sylius_addressing_address', $address);
    }
}