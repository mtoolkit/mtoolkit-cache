# MToolkit - Cache
The cache module of [MToolkit](https://github.com/mtoolkit/mtoolkit) framework.

## Cache types

- MySQL
- File
- APC

## Usages
Following, the examples of each type of supported cache.
 
### MySQL

```php
$cacheManager = MCacheFactoryImpl::getManager(new MCacheConfiguration(
    MCacheType::MYSQL,
    array(
        'db' => new PDO(...),
        'table' => 'mcache'
    )
));
```

### File

```php
$cacheManager = MCacheFactoryImpl::getManager(new MCacheConfiguration(
    MCacheType::FILE,
    array(
        'path' => '/temp/cache'
    )
));
```

### APC

```php
$cacheManager = MCacheFactoryImpl::getManager(new MCacheConfiguration(
    MCacheType::APC,
    array()
));
```