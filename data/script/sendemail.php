<?php

use Zend\Mail\Transport\Smtp;
    try{

        $options = unserialize(argv[1]);
        $message = unserialize(argv[2]);

        $transport = new Smtp($options);
        $transport->send($message);
    } catch (\Exception $ex) {
            echo $ex->getMessage();
            return false;
    }