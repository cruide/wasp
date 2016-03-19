<?php namespace App\Controllers;

  class Errors extends \Wasp\Controller
  {
      public function anyDefault()
      {
          return $this->actionShow404();
      }

      public function anyShow404()
      {
          set_http_status(404);
          return $this->ui->fetch('errors/error_404');
      }

      public function anyShow500()
      {
          set_http_status(500);
          return $this->ui->fetch('errors/error_500');
      }

      public function anyForbidden()
      {
          set_http_status(403);
          return $this->ui->fetch('errors/forbidden');
      }

      public function anyNotallowed()
      {
          set_http_status(405);
          return $this->ui->fetch('errors/notallowed');
      }

      public function anyUnauthorized()
      {
          set_http_status(401);
          return $this->ui->fetch('errors/unauthorized');
      }
  }
