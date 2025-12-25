# Siakad Project

## Overview
Siakad is a comprehensive academic management system designed to facilitate the management of student data, financial transactions, and scholarship information. This project includes various seeders to generate dummy data for testing and development purposes.

## Features
- **Student Management**: Manage student records, including personal information and academic details.
- **Financial Management**: Handle financial transactions, including billing and payments.
- **Scholarship Management**: Manage scholarship applications and recipients.
- **Academic Year Management**: Define and manage academic periods for student enrollment.

## Setup Instructions

### Prerequisites
- PHP >= 7.3
- Composer
- Laravel Framework

### Installation
1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd siakad
   ```
3. Install dependencies:
   ```
   composer install
   ```
4. Set up your `.env` file:
   ```
   cp .env.example .env
   ```
   Update the database configuration in the `.env` file.

5. Generate the application key:
   ```
   php artisan key:generate
   ```

### Running Migrations
Run the migrations to set up the database structure:
```
php artisan migrate
```

### Seeding the Database
To populate the database with dummy data, run the following command:
```
php artisan db:seed
```
This will execute the `DatabaseSeeder.php`, which orchestrates the execution of all individual seeders.

## Usage
After seeding the database, you can access the application and test its features. The dummy data generated will allow you to explore the functionalities without needing real data.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.