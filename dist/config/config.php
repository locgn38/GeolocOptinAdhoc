<?php
return array(
      "database" => array(
            "username" => "username",
            "password" => "userpass",
            "server" => "servername",
            "dbname" => "dbname"
      ),
      "thecallr" => array(
            "login" => "_login_",
            "password" => "_password_",
            "settings" => array(
                  "push_mo_enabled" => true,
                  "push_mo_url" => "_your_url_"
            ),
            "order" => array(
                  "set" => "sms.set_settings",
                  "send" => "sms.send"
            )
      ),
      "url" => ($_SERVER["SERVER_PORT"] == "443" ? "https":"http") . "://" . $_SERVER["HTTP_HOST"],
      "mail" => "mail@fqdn"

);
?>
