# Api Webshop

## Installation

Clone the repo: ``` git clone https://github.com/edmondzahiti/api_webshop.git ```

```cd``` into the folder generated

Run ```copy .env.example .env``` and after that update database credentials in ```.env``` file

Execute commands as below:

```sh 
composer install
php artisan key:generate
php artisan migrate --seed
php artisan import-masterdata
php artisan serve
```

#### Unit Test

```sh 
Execute command to run all the tests: ``` php artisan test ```  
Or Execute each test individually: ``` php artisan test --filter TestName ```  
```

#### I've implemented the following components in your Laravel application:

```sh 
- Controller: OrderController manages HTTP requests and delegates operations to the service layer.

- Service Layer (OrderService): It contains order-related business logic, such as creating, updating, deleting etc.

- Repository Layer (OrderRepository): This layer abstracts database operations for orders, ensuring separation of concerns.

- Request Validation: Laravel's request validation classes ensure incoming data meets specified criteria.

- Collections and Resources: I've employed collections and resources to format and structure API responses consistently.
```
