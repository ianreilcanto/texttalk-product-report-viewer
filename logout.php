<?php

session_start();
session_destroy();
header("Location:  http://webshopapi.cera.nu/index.php");