<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Mautic\FormBundle\Validator\Constraint\FileExtensionConstraintValidator;

return [
    'name'        => 'FastContactUpdate',
    'description' => 'Update contact fast with shortcut',
    'author'      => 'mtcextendee.com',
    'version'     => '1.0.0',
    'services'    => [
        'events'  => [
            'mautic.fast.contact.update.button.subscriber' => [
                'class'     => \MauticPlugin\MauticFastContactUpdateBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
        ],
        'forms'   => [
        ],
        'command' => [
        ],
        'other'   => [
        ],

        'helpers'      => [],
        'models'       => [
        ],
        'integrations' => [
        ],
    ],
    'routes'      => [
        'public' => [
            'mautic_fast_contact_update' => [
                'path'       => '/fast/contact/update/{objectId}',
                'controller' => 'MauticFastContactUpdateBundle:Contact:update',
            ],
        ],
        'main'   => [
        ],
    ],
    'menu'        => [
    ],
    'parameters'  => [
    ],
];
