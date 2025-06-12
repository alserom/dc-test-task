## How to use

1. Run `docker compose build --pull --no-cache` to build fresh images
2. Run `docker compose up --detach` to set up and start all containers
3. Run `docker compose exec php bin/console products:import` to parse products
4. Open `https://localhost/api/products?limit=5` in a browser to check the products list (REST endpoint)
5. Open the `products.csv` file in the root project dir to check the products list
6. Run `docker compose down --remove-orphans` to stop the Docker containers



#### Code Style Check
```bash
  docker compose exec php vendor/bin/phpcs
```

#### Static analyses
```bash
  docker compose exec php vendor/bin/phpstan analyse --memory-limit=-1
```

#### Tests
```bash
  docker compose exec php vendor/bin/phpunit
```

#### Parse products
```bash
  docker compose exec php bin/console products:import
```
