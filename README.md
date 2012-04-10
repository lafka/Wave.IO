# Wave IO handling

Very basic handling of stream data like a webservice client

```php
<?php

use Wave\IO\Client\HTTP as HttpClient;

$client = new HttpClient('http://api-endpoint.example.com');
$client->filter('application/xml');
$result = $client->request('/resource');

// handle your result array here
?>
```
