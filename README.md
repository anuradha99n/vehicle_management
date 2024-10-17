# Vehicle Management System

## Overview

The Vehicle Management System is a web application designed to streamline the management of vehicles, including tracking, maintenance, and documentation. Built using PHP and MySQL, this application leverages Docker for containerization, enabling consistent deployment and ease of scalability.

## Features

- **User-Friendly Interface**: Intuitive web interface for managing vehicle records.
- **Database Management**: Robust handling of vehicle data and user data with MySQL.
- **Containerization**: Utilizes Docker to create isolated environments for both the application and the database.
- **Scalability**: Easily scalable to accommodate a growing number of vehicles and users.

## Tech Stack

- **Frontend**: HTML, CSS
- **Backend**: PHP 8.1, Apache
- **Database**: MySQL
- **Containerization**: Docker, Docker Compose

## Getting Started

To get a local copy up and running, follow these steps:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/vehicle_management.git
   ```
   ```bash
   cd vehicle_management/vms
    ```

2. **Build and run the containers:**
    ```bash
    docker-compose up --build
    ```

3. **Access the application: Open your browser and navigate to**
    http://localhost:8081

 4. **Restore the database**
    * Copy the SQL backup file into the container:
    ```bash
        docker cp path/to/vms.sql vms_db_1:/vms.sql
    ```
    * Execute the restore command:
    ```bash
        docker exec -i vms_db_1 mysql -uroot -p{rootpassword} vehicle_management < /vms.sql
    ```

5. ** Stop Docker Container **
    ```bash
        docker stop {container_id_or_name}
    ```
    * You can stop multiple containers at once by listing their IDs/names separated by spaces 





