# Test Gac Technology - M'hemed BEN AOUN

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

**I spent 4 Hours to initialize, configure and develop**

Good code review ;) !

### Prerequisites

* [Git](https://git-scm.com)
* [Docker](https://www.docker.com/get-docker)

### Installing

1) Clone project.
```
https://github.com/mbenaoun/gac-technology.git
```

2) Build contener with docker.
```
docker-compose build
docker-compose up
```

We are 3 containers created in docker :

- gac-technology_php_1 : Server PHP (Application)
- gac-technology_mysql_1 : Server MySQL
- gac-technology_nginx_1 : Server Nginx

3) Run composer.

- Connection API Container

```
docker exec -it gac-technology_php_1 bash
```

- Launch composer install

```
composer install
```

4) Launch script migration DB

- Connection API Container

```
docker exec -it gac-technology_php_1 bash
```

- Launch script migration

```
php bin/console doctrine:migrations:migrate
```

or 

```
sf doctrine:migrations:migrate
```

or

```
sf d:m:m
```

5) Launch scripts for TEST GAC Technology

- Connection API Container

```
docker exec -it gac-technology_php_1 bash
```

- Inject data csv in db

```
php bin/console inject:data
```

or

```
sf inject:data
```

or

```
sf i:d
```

- Requests SQL to analyse data

```
php bin/console analyse:data
```

or

```
sf analyse:data
```

or

```
sf a:d
```
