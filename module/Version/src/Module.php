<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Version;

/**
 * Description of Module
 *
 * @author marcio
 */
class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

}