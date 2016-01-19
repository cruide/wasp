<?php

  if( !class_exists('Phar') ) {
      die('Class Phar not found.');
  }

  if( is_file('wasp.phar') ) {
      unlink('wasp.phar');
  }
  
  $arj = new Phar('wasp.phar');
  $arj->setStub('<?php define( \'CORE_PATH\', \'phar://\' . __FILE__ ); require( CORE_PATH . \'/bootstrap.php\' ); __HALT_COMPILER(); ?>');
  $arj->buildFromDirectory('wasp/');
  $arj->setSignatureAlgorithm(PHAR::MD5);

  if( Phar::canCompress(Phar::GZ) ) {
      $arj->compressFiles(Phar::GZ);
  } else if( Phar::canCompress(Phar::BZ2) ) {
      $arj->compressFiles(Phar::BZ2);
  }  

  echo 'wasp.phar created!';
  