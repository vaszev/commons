# Commons

##Collection of common commands
document:clear, project:clear:cache, project:size

##Collection of common functions

genSimplePassword, getImageVariant, incFileNameIfExists, loremIpsum, passwordEncode, passwordVerify, refreshSecurityToken, removeAccents, repairFileName, replaceNonAlphanumericChars, addressToCoords, entityCheck, friendlyFilter

##Collection of common twig extensions

dayNameFilter, friendlyFilter, imgSizeFilter, minutesTimeFilter, priceFilter, entityCheck, lorem, rnd, friendly

###how to **install**:
https://packagist.org/packages/vaszev/commons-bundle

via **composer**:
```
$ composer install "vaszev/commons-bundle":"~2.0"
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
