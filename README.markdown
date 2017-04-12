# phpWowUuid

Wow! Uuid for PHP. Simple and Fast. Compliance with RFC 4122, but only UUID version 1 and version 4. And support snowflake-like algorithms.

## Requirement

PHP 5.3+

## Usage

### Standalone WowLog library

```
include __DIR__.'/src/Wow/Util/Uuid.php';

use \Wow\Util\Uuid as Uuid;

$uuid1 = Uuid::v1();
$uuid4 = Uuid::v4();
$uuid1_order = Uuid::v1_order(false);
$snowflake = Uuid::snowflake(1, 1);
$snowflake_random = Uuid::snowflake_random();
$snowflake_order = Uuid::snowflake_order(1, 1);
```

### Work with Composer

#### Edit `composer.json`

```
{
    "require": {
        "yftzeng/wow-uuid": "dev-master"
    }
}
```

#### Update composer

```
$ php composer.phar update
```

#### Sample code
```
include 'vendor/autoload.php';

use \Wow\Util\Uuid as Uuid;

$uuid1 = Uuid::v1();
$uuid4 = Uuid::v4();
$uuid1_order = Uuid::v1_order(false);
$snowflake = Uuid::snowflake(1, 1);
$snowflake_random = Uuid::snowflake_random();
$snowflake_order = Uuid::snowflake_order(1, 1);
```

## License

the MIT License
