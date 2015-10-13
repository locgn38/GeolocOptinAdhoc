--------------------------------

# GeolocOptinAdhoc

--------------------------------

Géolocalisation consentie (opt-in) contextualisée (ad-hoc) et temporaire (éphémère).

## Setup

* PHP 5.3 or higher required
* Postgresql database needed, with postgis extension enabled

You need an account on www.thecallr.com to send SMS

### Configuration

Configuration takes place in /config/config.php

* Configure an Apache VirtualHost with document_root pointing to this folder,
and mod_rewrite enabled
* all requests handled by index.php with .htaccess
* routes are defined in /src/Request.php
* autoloader configuration in /src/Loader.php
