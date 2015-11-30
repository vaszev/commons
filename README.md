# Commons
##Collection of common functions

genSimplePassword, getImageVariant, incFileNameIfExists, loremIpsum, passwordEncode, passwordVerify, refreshSecurityToken, removeAccents, repairFileName, replaceNonAlphanumericChars

##Collection of common twig extensions

dayNameFilter, friendlyFilter, imgSizeFilter, minutesTimeFilter, priceFilter, entityCheck

###how to **install**:
https://packagist.org/packages/vaszev/commons-bundle

via **composer**:
```
$ composer install "vaszev/commons-bundle":"1.0.3"
```

set parameters in the **config.yml**:
```yaml
vaszev_commons:
    default_image: '../Resources/public/img/tr.png'
    docs: '/uploads/documents/'
    image_variations:
      small: [100,50]
      medium: [200,150]
      large: [600,400]
      .
      .
      .
      yourvariation: [111,222]
```

in your **AppKernel.php**:
```php
new Vaszev\CommonsBundle\VaszevCommonsBundle(),
```
