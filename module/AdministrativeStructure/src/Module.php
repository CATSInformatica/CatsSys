<?php

namespace AdministrativeStructure;

/**
 * Description of Module
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
