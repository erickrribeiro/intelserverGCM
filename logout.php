<?php
/**
 * Created by PhpStorm.
 * User: lgpbentes
 * Date: 15/10/16
 * Time: 11:54
 */

session_start();
if(session_destroy()) // Destroying All Sessions
{
    header("Location: /intelserverGCM");
}
?>