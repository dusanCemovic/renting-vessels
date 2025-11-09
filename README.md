# Renting Vessels (Laravel project)

Laravel application implementing:
App for renting vessels based on equipment, and it needs to be free in that period.

## Key features
- Manage Vessels with type, size and equipment
- Make reservations ensuring: vessel free & has required equipment
- If no vessel available at requested time, system suggests earliest possible time
- Adding vessel and maintenance and equipment records
- Equipment is a separate entity and is attached to vessels with a pivot table. Add new equipment easily.
- The reservation algorithm is centralized in VesselReservation service (main are checkAvailability and getSuggestions).


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


# Notes on modularity & extensibility
- General 
  - Home is listing all reservations and maintenances in table
- Vessel
  - Has name, type, size and equipment. 
  - CRUD. Delete is soft. When updating, equipment can be changed.
  - Relations with equipments(BelongsToMany), reservations and maintenances.
- Equipment
  - Has code, name and description
  - CRUD. When deleting, it is also detached from relation with vessel.
  - Relations with vessels (BelongsToMany)
- Reservation 
  - Has title, description, vessel_id, start_at, end_at, required_equipment and status.
  - Can be stored, list all or show single one
  - Storing has 3 main parts
    - Getting vessels based on required equipment
    - Checked availability and allow to create reservation
    - Return suggestion to user based on first upcoming free slot
- Maintenance
  - Has title, vessel_id, start_at, end_at and notes.
  - Can be stored and be listed on each vessel separate or togather
  - Storing has 2 main parts
    - Checked availability and allow to create maintenance
    - Return suggestion to user based on first upcoming free slot, but as validation error on form
- Request
  - Each request has own extended class
- Services
  - Repository
    - Those are repository which gets all reservations get maintenances and sorting which is used for reading on different views with table
    - Method dateFromLocalToDB is used to parsed date from view/form and change to UTC format which is stored in DB. Important to notice that tests are using when putting direct time into db.
  - VesselReservation (only main methods will be described)
    - checkAvailability - based on start and end, this function check if vessel is free. To optimize, we are sending back free vessel with the least equipment as possible if we have more then one free.
    - getSuggestions - collect all reservations and maintenance and for each vessel return first free slot. It is ordered by time.
- Other:
  - We created custom blade "slDate" with will present right time for local
- Test: 
  - Major top 3 edge-cases are tested in: 
    - test_creates_reservation_when_vessel_available
    - test_returns_suggestions_when_no_vessel_available
    - test_local_time_conflict_with_existing_reservation

## Notes

- Assumption is that we have more maintenances than reservations, so we first check maintenance for specific period. If we found one then we stop searching.
- If algorithm send back more then one free vessel, then we are reserving the one with at least possible additional equipments then user from form marked as required.
- Indexing for sorting vessels is added, so that function will be much faster. This possibilities can be done for other tables.
- Possible adding is that we may check reservations and maintenance not all records at the same time, but in batches of time. E.g. Only same day what user wanted. If not then try second iteration with two next days and so on.

