<?php 


$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tempaccess`(
  `id_tempaccess` int(11) NOT NULL auto_increment,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `temp_email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tempaccess`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

if ( is_array( $sql ) && ! empty( $sql ) ) {
    foreach ( $sql as $sq ) :
        if ( ! Db::getInstance()->Execute( $sq ) ) {
            return false;
        }
    endforeach;
};