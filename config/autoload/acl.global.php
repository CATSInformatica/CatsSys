<?php

return array(
    'acl' => array(
        'redirect_route' => array(
            'params' => array(
                'controller' => 'Authorization\Controller\Index',
                'action' => 'index',
                //'id' => '1',
            ),
            'options' => array(
                // We should redirect to an action Controller accessable for everyone.
                'name' => 'authorization', // display forbidden error,
            ),
        ),
    )
);
