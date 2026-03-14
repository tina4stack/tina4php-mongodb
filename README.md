# tina4php-mongodb

MongoDB database driver for the Tina4 PHP framework. Works seamlessly with the Tina4 ORM — write standard SQL and the built-in NoSQL parser translates it to native MongoDB operations.

[![Tests](https://github.com/tina4stack/tina4php-mongodb/actions/workflows/tests.yml/badge.svg)](https://github.com/tina4stack/tina4php-mongodb/actions/workflows/tests.yml)

## Installing

```bash
composer require tina4stack/tina4php-mongodb
```

### Requirements

- PHP >= 8.1
- MongoDB PHP extension (`ext-mongodb`)
- MongoDB PHP library (`mongodb/mongodb`)

## Usage

### Basic Connection

```php
global $DBA;

// Local MongoDB
$DBA = new \Tina4\DataMongoDb("localhost/27017:myDatabase");

// With authentication
$DBA = new \Tina4\DataMongoDb("localhost/27017:myDatabase", "username", "password");
```

### With Tina4 ORM

```php
class User extends \Tina4\ORM
{
    public $tableName = "users";
    public $id;
    public $name;
    public $email;
}

// Create
$user = new User();
$user->name = "Andre";
$user->email = "andre@example.com";
$user->save();

// Read
$user = new User();
$user->load("name = 'Andre'");
echo $user->name;

// Update
$user->email = "new@example.com";
$user->save();
```

### Direct Queries

```php
global $DBA;

// Insert
$DBA->exec("insert into users (name, email) values (?, ?)", "Andre", "andre@example.com");

// Select
$result = $DBA->fetch("select name, email from users where name = 'Andre'");

// Update
$DBA->exec("update users set email = 'new@example.com' where name = 'Andre'");

// Delete
$DBA->exec("delete from users where name = 'Andre'");
```

## Docker (for testing)

```bash
docker run -d --name tina4_mongo -p 27017:27017 mongo
```

## Running Tests

```bash
composer test
```

---

## Our Sponsors

**Sponsored with 🩵 by Code Infinity**

[<img src="https://codeinfinity.co.za/wp-content/uploads/2025/09/c8e-logo-github.png" alt="Code Infinity" width="100">](https://codeinfinity.co.za/about-open-source-policy?utm_source=github&utm_medium=website&utm_campaign=opensource_campaign&utm_id=opensource)

*Supporting open source communities <span style="color: #1DC7DE;">•</span> Innovate <span style="color: #1DC7DE;">•</span> Code <span style="color: #1DC7DE;">•</span> Empower*
