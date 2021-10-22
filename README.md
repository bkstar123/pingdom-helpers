# Pingdom Helper Scripts

This repository provides some helper scripts to interact with Pingdom API. 

## Usage

### Installation

- Firstly, download the repository to your local machine and run ```composer install``` to install all the dependent packages.  
- Rename ```.env.example``` to ```.env```, put your Pingdom API token to ```.env```  file  
- Rename ```input/2.checks.example``` to ```input/2.checks```  
- Rename ```input/2.date_range.php.example``` to ```input/2.date_range.php```  

### Execution

```bash
# Make sure you are in the root of the package
php scripts/<script_name.php>
```

### Execution via Docker

You can use php & composer runtime environment provided via ```bkstar123/php-composer``` Docker container as follows:   
```bash
# Make sure you are in the root of the package
docker run --rm -v $PWD:/app bkstar123/php-composer:latest composer install  # To install package dependencies
docker run --rm -v $PWD:/app bkstar123/php-composer:latest php scripts/<script_name.php>
```