# Renting Vessels (Laravel project)

Laravel application implementing:
App for renting vessels based on equipment, and it needs to be free in that period (no maintenance or reservations)

## Key features
- Manage Vessels with type, size and equipment
- Make reservations ensuring: vessel free & has required equipment
- If no vessel available at requested time, system suggests earliest possible time
- Adding vessels and maintenance records
- Delete vessels or equipment

# Notes on modularity & extensibility
- Equipment is a separate entity and is attached to vessels with a pivot table. Add new equipment easily.
- Adding new vessels is a simple model create + attach equipment.
- The reservation algorithm is centralized in ReservationController::reserve.

## Requirements

- Used PHP v8.3.6 & Laravel 12.36.1
- Mysql 8+
- Composer

## Setup

1. Clone repo https://github.com/dusanCemovic/renting-vessels and go into folder `cd renting-vessels`
2. Composer:
   ```
    composer install
   ```
3. COPY env example
   ```
    cp .env.example .env
   ```
4. Run generating key:
   ```
   php artisan key:generate
   ```
5. Create file for sqlite:
   ```
   touch database/database.sqlite
   ```
6. Run Migration:
   ```
   php artisan migrate:fresh --seed
   ```
7. Start Server:
    ```
    php artisan serve
    ```
8. Run tests:
    ```
    php artisan test
    ``` 

## Notes
