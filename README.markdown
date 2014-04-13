# phpWowUuid

Wow! Uuid for PHP. Simple and Fast. Compliance with RFC 4122, but only UUID version 1 and version 4.

## Requirement

PHP 5.3+

## Usage

### Standalone WowLog library

```
include __DIR__.'/src/Wow/Util/Uuid.php';

use \Wow\Util\Uuid as Uuid;

$uuid1 = Uuid::v1();
$uuid4 = Uuid::v4();
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
```

## License

the MIT License
