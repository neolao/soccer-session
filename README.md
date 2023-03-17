# soccer-session

## Installation

1. Create `users.json` based on `users.dist.json` and make it writable by PHP
2. Make the folder `sessions` writable by PHP
3. The web server should target the folder `www`

### Example for nginx with php-fpm

```nginx
server {
  root /path/to/soccer-session/www;
  server_name soccer-session.com;

  location / {
    index index.php;
  }

  error_page 404 /404.html;

  location ~ .php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    include /etc/nginx/fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
  }
}
```

## How to create a user

A user has an identifier and a name. And the identifier is unique.

The file `users.json` represents a key-value dictionary. The key is the user identifier and the value is the user properties.

To create an administrator, you must set the `type` property of the user to `admin`.

Example:
```json
{
  "1234": {
    "name": "John Doe",
    "type": "admin"
  },
  "5678": {
    "name": "Sarah Croche"
  }
}
```
