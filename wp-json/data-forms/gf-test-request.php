<?php 
    echo 'test<br><pre>';
    $result = $_POST;
    //phpinfo();
    //ob_flush();
    //ob_start();

        //var_dump($result);
        //echo '</pre>';

    file_put_contents( 
        "log.txt", 
        file_get_contents("log.txt") ."\n"
            .date('h:i:s') ."\n"
            .json_encode($result) ."\n"
            ."\n"
    );

    //ob_end_flush();
