<?php

//    +--------------+-------------+----------------+----------------------------+---------+
//    | privilege_id | resource_id | privilege_name | privilege_permission_allow | role_id |
//    +--------------+-------------+----------------+----------------------------+---------+
//    |            1 |           5 | all            |                          1 |       0 |
//    |            2 |           6 | login          |                          1 |       0 |
//    |            3 |           6 | all            |                          1 |       1 |
//    |            4 |           7 | all            |                          1 |       0 |
//    |            5 |           8 | index          |                          1 |       0 |
//    |            6 |           9 | index          |                          1 |       1 |
//    |            7 |           9 | create         |                          1 |       2 |
//    |            8 |           9 | delete         |                          1 |       2 |
//    |            9 |           9 | edit           |                          1 |       2 |
//    |           10 |          10 | all            |                          1 |       2 |
//    +--------------+-------------+----------------+----------------------------+---------+



//                        +---------+-----------+
//                        | role_id | role_name |
//                        +---------+-----------+
//                        |       0 | guest     |
//                        |       1 | basic     |
//                        |       2 | admin     |
//                        +---------+-----------+


//            +-------------+---------------------------------+
//            | resource_id | resource_name                   |
//            +-------------+---------------------------------+
//            |           1 | all                             |
//            |           2 | Public Resource                 |
//            |           3 | Private Resource                |
//            |           4 | Admin Resource                  |
//            |           5 | Site\Controller\Index           |
//            |           6 | Authentication\Controller\Login |
//            |           7 | Authorization\Controller\Index  |
//            |           8 | UMS\Controller\Index            |
//            |           9 | Authentication\Controller\User  |
//            |          10 | Authorization\Controller\Role   |
//            +-------------+---------------------------------+


//select privilege_id, resource_name, privilege_name, role_name, privilege_permission_allow 
//from privilege, role, resource 
//where privilege.resource_id = resource.resource_id and role.role_id = privilege.role_id;

/**
 * update privilege set privilege_name = 'index' where privilege_id = 1;
 */

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
        
//        'roles' => array(
//            'guest' => null,
//            'member' => 'guest',
//            'admin' => 'member',
//        ),
//        'resources' => array(
//            'allow' => array(
//                'Site\Controller\Index' => array(
//                    'all' => 'guest',
//                ),                
//                'Authentication\Controller\Login' => array(
//                    'login' => 'guest',
//                    'all' => 'member',
//                ),
//                'Authorization\Controller\Index' => array(
//                    'all' => 'guest',
//                ),
//                'UMS\Controller\Index' => array(
//                    'index' => 'guest',
//                ),
//                'UMS\Controller\User' => array(
//                    'index' => 'member',
//                    'create' => 'admin',
//                    'delete' => 'admin',
//                    'edit' => 'admin',
//                ),
//                'UMS\Controller\Role' => array(
//                    'all' => 'admin',
//                )
//            )
//        )
    )
);
