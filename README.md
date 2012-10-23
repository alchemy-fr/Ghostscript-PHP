# Ghostscript PHP driver

[![Build Status](https://secure.travis-ci.org/alchemy-fr/Ghostscript-PHP.png)](http://travis-ci.org/alchemy-fr/Ghostscript-PHP)

example of use :

```php
use Ghostscript\PDFTranscoder;

$transcoder = PDFTranscoder::load();
$transcoder->open('document.pdf')
            ->transcode('firstPage.pdf', 1, 1)
            ->close();
```


# License

Released under the MIT License
