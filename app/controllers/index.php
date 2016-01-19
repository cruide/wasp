<?php namespace App\Controllers;

use \App\Models\User;
use \App\Models\UsersProfile;

  class Index extends \Wasp\Controller
  {
// ------------------------------------------------------------------------------
      /**
      * Этот метод будет запущен перед выполнением
      * любого экшена
      */
      public function _before()
      {

      }
// ------------------------------------------------------------------------------
      /**
      * Метод-экшен по умолчанию
      * 
      */
      public function actionDefault()
      {
          return $this->ui->fetch('index');
      }
  }
