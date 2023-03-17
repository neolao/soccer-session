# soccer-session

## Installation

1. Create `users.json` and make it writable by PHP
2. Make the folder `session` writable by PHP
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
