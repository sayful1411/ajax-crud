
# AJAX PROJECT

The is simple a product curd project using ajax



# How you setup and run this project -

> step 1: clone repo 

    https://github.com/sayful1411/exam

> step 2: go to barta

```bash
  cd exam
  ``` 
  
>  step 3: edit .env.example to .env
   
> step 4: run composer install

```bash
  composer install
  ``` 
  
  > step 5: generate a new key
  
```bash
  php artisan key:generate
  ``` 
  
  >  step 6: create a new database 
  
    
  >  step 7: run migration 

```bash
  php artisan migrate --seed (I am used sqlite database. It will ask a prompt to create sqlite database)
  ``` 
  
  >  step 8: run project
  
```bash
  php artisan serve
  ```
