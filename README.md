
# AJAX PROJECT

This is a simple product curd project using Ajax

# Admin Login -

>    email: admin@gmail.com

>password: password

# How you set and run this project -

> Step 1: clone repo 

```bash
git clone https://github.com/sayful1411/ajax-crud.git && cd ajax-crud
```
  
> Step 2: create .env

```bash
cp .env.example .env
```
   
> Step 3: run composer install

```bash
composer install
``` 
  
> Step 4: generate a new key
  
```bash
php artisan key:generate
``` 
 
>  Step 5: run migration
> (I used SQLite database. It will ask a prompt to create SQLite database)

```bash
php artisan migrate --seed 
``` 
  
>  Step 6: run project
  
```bash
php artisan serve
```
