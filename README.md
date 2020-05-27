# Migrate CSV file to database (Postgres)

This Laravel package allow migrate CSV file to database, creating the database structure and importing the data.

### Installation

```bash 
composer require edersoares/laravel-csv-database
```

### Usage

```bash
# Create Laravel migration to represent CSV file
php artisan csv:database:migration FILENAME TABLE

# Run migrate
php artisan migrate

# Import CSV file to database 
php artisan csv:database:import FILENAME TABLE
```
