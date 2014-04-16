hush
====

hush is intended to be a messaging service with JS powered client side encryption.

Currently no encryption is implemented and work is mostly centered around creating a stable messaging application.

# Installation

1. Download the latest [release](https://github.com/daniel-bell/hush/releases)
2. Create a TLS certificate
3. Set up Apache or [Nginx](http://wiki.nginx.org/Symfony)
4. Optionally (but recommended) enable Perfect Forward Secrecy for [Apache](https://scottlinux.com/2013/06/26/how-to-enable-perfect-forward-secrecy-in-apache-on-linux/) or [Nginx](https://scottlinux.com/2013/07/16/configure-nginx-for-pfs-and-ssllabs-com-a-rating/)
5. Run ```composer install```
6. Run ```php app/console doctrine:database:create```
7. Run ```php app/console doctrine:schema:update --force```
8. Done
