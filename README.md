# GreatFood SDK & Application

---

## Prerequisites

To run this project, you need:
* **Docker & Docker Compose** (Recommended)
* **Make** (Optional, for easy shortcuts)

If you are running **without Docker**, you will need:
* **PHP 8.4+**
* **Composer**

---

## Option 1: Running with Docker (Recommended)

This method ensures the environment matches the development setup perfectly.

1. **Build the containers:**
   ```bash
   make build
   ```
2. **Start the containers:**

    ```bash
    make run
   ```
3. **Install dependencies:**

    ```bash
    make install
   ```
4. **Run the business scenarios::**

    ```bash
    make run-scenarios
    ```
Note: If make is not installed on your system, run:
```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php src/run.php
```
---

## Option 2: Running Locally (No Docker)
Use this if you prefer to run the script directly on your host machine.

Install dependencies:

```Bash
composer install
```
Setup Environment:
Create a .env file in the root directory:
```
API_BASE_URL=[https://api.greatfood.ltd](https://api.greatfood.ltd)
API_AUTH_METHOD=oauth
API_CLIENT_ID=test_user
API_CLIENT_SECRET=test_password
# Use FileMockApiClient for local testing without a real server
API_CLIENT_IMPLEMENTATION='WiQ\Sdk\Client\FileMockApiClient'
```
Run the application:

```Bash
php src/run.php 
```
