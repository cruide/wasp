<?php namespace App\Controllers;

  class Errors extends \Wasp\Controller
  {
      public function actionDefault()
      {
          return $this->actionShow404();
      }

      public function actionShow404()
      {
          set_http_status(404);
          return $this->ui->fetch('errors/error_404');
      }

      public function actionShow500()
      {
          set_http_status(500);
          return $this->ui->fetch('errors/error_500');
      }

      public function actionForbidden()
      {
          set_http_status(403);
          return $this->ui->fetch('errors/forbidden');
      }

      public function actionNotallowed()
      {
          set_http_status(405);
          return $this->ui->fetch('errors/notallowed');
      }

      public function actionUnauthorized()
      {
          set_http_status(401);
          return $this->ui->fetch('errors/unauthorized');
      }
  }
