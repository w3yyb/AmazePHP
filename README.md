# AmazePHP
## AmazePHP  
A good choice for starting a PHP small project, especially for APIs.   
It only takes 1 minute to start a project.


## features:  
- configuration  
- .env  
- router    
- mysql class  
- http client  
- log   


## install:    
git clone https://github.com/w3yyb/AmazePHP.git  
composer install  

 ## run
 php -S 0.0.0.0:9080  public/index.php  
 open http://localhost:9080

 ## requirements:  
 php 8.1  

 ## Directory Structure  

 ### The App Directory  
 The app directory contains the core code of your application. We'll explore this directory in more detail soon; however, almost all of the classes in your application will be in this directory.  
 ### The config Directory  
 The config directory, as the name implies, contains all of your application's configuration files.  Include the route config file.
  ### The helper Directory  
The helper functions in it.
### The lib Directory
The framework core directory, include some lib class. And you can put  your class file in it.
### The public Directory
The public directory contains the index.php file, which is the entry point for all requests entering your application and configures autoloading. This directory also houses your assets such as images, JavaScript, and CSS.

