<?php
$options = getopt(null, array("config:"));
if ( isset($options['config']) ) {
    if ( !file_exists($options['config']) ) {
        echo "Config file {$options['config']} not found\n";
        exit(1);
    }
    include $options['config'];
} else {
    include __DIR__.'/updater_config-prod.php';
}
