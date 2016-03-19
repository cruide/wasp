# WASP PHP5 MVC micro-framework.
Маленький и быстрый PHP 5.4+ MVC micro-framework.

Вам необходимо написать простое веб-приложение легко и быстро?

Нет ничего проще! WASP для этого и создан!

- Минимум настроек;
- MVC структура;
- Понятный и простой код;
- Удобный интерфейс для работы с Базой Данных;
- Поддержка мульти-язычности

Обратите внимание на то, что в данной ветке для работы с базой используется [Laravel Eloquent ORM](https://github.com/LaravelRUS/docs/blob/5.1/eloquent.md)

## Шаблонизатор Smarty

Для проекта в качестве шаблонизатора используется [Smarty template engine](http://smarty.net)


## Структура приложения
### Директории:

- app
    - controllers
        - index.php
    - services
        - my_service.php
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

/* Для удобства введены функции-алиасы */
$id = input()->get('id');

```

в методе контроллера: 
```PHP
<?php

class Users extexnds \Wasp\Controller
{
    public function anyEdit()
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
    public function anyEdit( $id = null )
    {
        
    }
}
```   

**Для того, что бы задать свой роут:**

/app/routes.php

```PHP
<?php return [
    ['regexp' => '^edit\/([0-9]+)$', 'replace' => 'users/edit/id/$1'],
];
```

Из примера видно, что URL вида: http://my-site.local/edit/20

Будет интерпретирован как: http://my-site.local/users/edit/id/20

### Контроллер

Пример контроллера:
```PHP
<?php namespace App\Controllers;

class Index extends \Wasp\Controller
{
    /* Будет запущен перед любым методом контроллера */
    public function _before()
    {
        
    }
    
    /* Для любых запросов */
    public function anyDefault()
    {
        return $this->ui->fetch('index');    
    }

    /* Только для GET запросов */
    public function getEdit( $user_id = null )
    {

    }

    /* Только для POST запросов */
    public function postEdit( $user_id = null )
    {

    }
}
```

В данном примере видно, что можно задавать тип запроса.

- any - это любой тип запроса, если нет альтернатив;
- get - для GET запросов;
- post - для POST запросов;
- put - для PUT запросов;
- delete - для DELETE запросов;

Метод anyDefault обязателен. Он используется как метод по умолчанию если в запросе не указать метод.

Т.е. если запрос будет http://my-site.local/users, то это будет интерпритироваться как http://my-site.local/users/default .


## Работа с базой данных

Для работы с базой данных используется библиотека [Laravel Eloquent ORM](https://github.com/LaravelRUS/docs/blob/5.1/eloquent.md)

Использование данной ORM сделает работу с приложением очень гибкой и удобной.

### Пример модели
```PHP
<?php namespace App\Models;

class User extends \Wasp\Model
{
    public function profile()
    {
        return $this->hasOne('\\App\\Models\\UsersProfile');
    }
    
    public function session()
    {
        return $this->hasOne('\\App\\Models\\UsersSession');
    }
    
    public function group()
    {
        return $this->belongsTo('\\App\\Models\\UsersGroup', 'group_id');
    }
}
```

## PHAR

Для более удобного использования, я предусмотрел возможность собрать ядро wasp в виде PHAR архива.

Для этого используйте файл make-phar.php

# Внимание!

Для создания архивов PHAR, Вам необходимо разрешить создание PHAR в php.ini

В index.php пропишите require('/путь/wasp.phar') вместо require('wasp/bootstrap.php') и всё.

## P.S.

Прошу не судить строго. Проект в начале своего пути и постоянно дорабатывается.

