<?php
/**
 * Default config header
 *
 * To replace this add a file /home/bout/code/php/taoTrunk/taoResultServer/config/header/default_resultserver.conf.php
 */
use \oat\taoOutcomeRds\model\RdsResultStorage;

return new RdsResultStorage([
    RdsResultStorage::OPTION_PERSISTENCE => 'default'
]);
