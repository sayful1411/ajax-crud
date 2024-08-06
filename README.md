
# AJAX PROJECT

This is a simple product curd project using Ajax

# How you set and run this project -

> Step 1: clone repo 

```bash
git clone https://github.com/sayful1411/ajax-crud.git && cd ajax-crud
```
   
> Step 2: run composer install

```bash
composer install
``` 

> Step 3: create .env

```bash
cp .env.example .env
```
  
> Step 4: generate a new key
  
```bash
php artisan key:generate
``` 

> Step 5: link storage
  
```bash
php artisan storage:link
``` 


> Step 6: change APP_URL from .env
  
```bash
APP_URL=http://localhost:8000
``` 
 
>  Step 7: run migration
> (I used SQLite database. It will ask a prompt to create SQLite database)

```bash
php artisan migrate --seed 
``` 
 
>  Step 8: run project
  
```bash
php artisan serve
```

# Admin Login -
> http://127.0.0.1:8000/admin/login