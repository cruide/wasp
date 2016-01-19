# WASP PHP5 MVC micro-framework.
Маленький и быстрый PHP 5.4+ MVC micro-framework.

Вам необходимо написать простое веб-приложение легко и быстро?

Нет ничего проще! WASP для этого и создан!

- Минимум настроек;
- MVC структура;
- Понятный и простой код;
- Удобный интерфейс для работы с Базой Данных;
- Поддержка мульти-язычности

Обратите внимание на то, что в данной ветке для работы с базой используется Laravel Eloquent ORM

## Структура приложения
### Директории:

- app
    - controllers
        - index.php
    - hooks
        - myhook.php
    - library
    - i18n
        - en.php
        - ru.php
    - models
        - mymodel.php
    - settings
        - config.ini
        - dbase.ini
    - bootstrap.php
    - functions.php
    - routes.php
- content
- logs
- themes
    - bootstrap
        - css
        - js
        - images
        - views
            - index.phtml
        - layout.phtml
- tmp
- .htaccess
- index.php

### Роутинг
Система роутинга упрощена до минимума с учетом возможности задавать свои варианты роутов.

Для того чтобы вызвать метод контроллера, требуется создать URL запроса в виде:
http://my-site.local/[контроллер]/[метод]

Что бы передать простой параметр, можно использовать:
http://my-site.local/[контроллер]/[метод]/id/20

Получить id можно двумя способами:

```PHP
<?php

$id = \Wasp\Input::MySelf()->get('id');
```

в методе контроллера: 
```PHP
<?php

class Users extexnds \Wasp\Controller
{
    public function action_Edit()
    {
        $id =  $this->input->get('id');
    }
}
```   

или так:

```PHP
<?php
class Users extexnds \Wasp\Controller
{
    public function action_Edit( $id = null )
    {
        
    }
}
```   

**Для того, что бы задать свой роут:**

/app/routes.php

```PHP
<?php return [
    ['regexp' => '^user\/([0-9]+)$', 'replace' => 'users/edit/id/$1'],
];
```

Из примера видно, что URL вида: http://my-site.local/user/20

Будет интерпретирован как: http://my-site.local/users/edit/id/20

### Контроллер

Пример контроллера:
```PHP
<?php namespace App\Controllers;

class Index extends \Wasp\Controller
{
    public function _before()
    {
        
    }
    
    public function action_Default()
    {
        return $this->ui->fetch('index');    
    }
}
```

## Работа с базой данных

Для работы с базой данных используется библиотека Laravel Eloquent ORM

### Пример модели
```PHP
<?php namespace App\Models;

class User extends \Wasp\Model
{
    public function profile()
    {
        return $this->hasOne('\\App\\Models\\UsersProfile')->first();
    }
    
    public function session()
    {
        return $this->hasOne('\\App\\Models\\UsersSession')->first();
    }
    
    public function group()
    {
        return $this->belongsTo('\\App\\Models\\UsersGroup', 'group_id')->first();
    }
}
```

Initiator: Тищенко Александр

Date of inception: 01/02/2015