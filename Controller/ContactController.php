<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticFastContactUpdateBundle\Controller;

use Mautic\FormBundle\Controller\FormController;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;

class ContactController extends FormController
{
    /**
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function updateAction($objectId)
    {
        //set some permissions
        $permissions = $this->get('mautic.security')->isGranted(
            [
                'lead:leads:viewown',
                'lead:leads:viewother',
                'lead:leads:create',
                'lead:leads:editown',
                'lead:leads:editother',
                'lead:leads:deleteown',
                'lead:leads:deleteother',
                'lead:imports:view',
                'lead:imports:create',
            ],
            'RETURN_ARRAY'
        );

        if (!$permissions['lead:leads:editown'] && !$permissions['lead:leads:editother']) {
            return $this->accessDenied();
        }

        $integration = $this->get('mautic.helper.integration')->getIntegrationObject('FastContactUpdate');

        if (false === $integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            return;
        }
        $settings = $integration->mergeConfigToFeatureSettings();
        if (empty($settings['field'])) {
            return;
        }


        $flashes = [];
        /** @var LeadModel $model */
        $model = $this->getModel('lead.lead');
        /** @var Lead $entity */
        $entity          = $model->getEntity($objectId);
        $contentTemplate = 'MauticLeadBundle:Lead:index';
        $activeLink      = '#mautic_contact_index';
        $mauticContent   = 'lead';
        $page            = $this->get('session')->get('mautic.lead.page', 1);
        $returnUrl       = $this->generateUrl('mautic_contact_index', ['page' => $page]);

        $model->setFieldValues($entity, [$settings['field'] => $settings['update']], true);
        $model->saveEntity($entity);

        $flashes[]      = [
            'type' => 'notice',
            'msg'  => $this->translator->trans(
                'mautic.fast.contact.update.updated',
                [
                    '%url%'  => $this->generateUrl('mautic_contact_action', [
                        'objectAction' => 'edit',
                        'objectId'     => $entity->getId(),
                    ]),
                    '%name%' =>  $entity->getPrimaryIdentifier(),
                    '%id%' =>  $entity->getId(),
                    '%contact%' => '#'.$entity->getId().' ('.$entity->getEmail().')',
                    '%field%'   => $settings['field'],
                    '%value%'   => $settings['update'],
                ]
            ),

            'msgVars' => [
                '%name%' => $entity,
                '%id%'   => $objectId,
            ],
        ];
        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => [
                'page' => $page,
            ],
            'contentTemplate' => $contentTemplate,
            'passthroughVars' => [
                'activeLink'    => $activeLink,
                'mauticContent' => $mauticContent,
            ],
        ];

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                [
                    'flashes' => $flashes,
                ]
            )
        );
    }
}
