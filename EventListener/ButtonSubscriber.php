<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticFastContactUpdateBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomButtonEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Templating\Helper\ButtonHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;

class ButtonSubscriber extends CommonSubscriber
{
    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    private $event;


    /**
     * ButtonSubscriber constructor.
     *
     * @param IntegrationHelper $helper
     */
    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_BUTTONS => ['injectViewButtons', 0],
        ];
    }

    /**
     * @param CustomButtonEvent $event
     */
    public function injectViewButtons(CustomButtonEvent $event)
    {

        if (FALSE === strpos($event->getRoute(), 'mautic_contact_')) {
            return;
        }
        if (null === $event->getItem()) {
            return;
        }

        $integration = $this->integrationHelper->getIntegrationObject('FastContactUpdate');

        if (false === $integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            return;
        }
        $settings = $integration->mergeConfigToFeatureSettings();
        if (empty($settings['field'])) {
            return;
        }


        /** @var CustomButtonEvent $event */
        $route = $this->router->generate(
            'mautic_fast_contact_update', ['objectId'=>$event->getItem()->getId()]
        );
        $attr  = [
            'href' => $route,
        ];

        $attr['data-toggle'] = 'ajax';
        $attr['data-method'] = 'post';
        $attr['data-target'] = '';

        $button =
            [
                'attr'      => $attr,
                'btnText'   => $settings['label'],
                'iconClass' => 'fa fa-link',
                'priority'  => -1,
            ];
        $event
            ->addButton(
                $button,
                ButtonHelper::LOCATION_LIST_ACTIONS,
                'mautic_contact_index'
            );

    }

}
