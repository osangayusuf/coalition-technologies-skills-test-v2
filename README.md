# Coalition Technologies Test V2

## Overview

This project is a simple product management application built using Laravel. It allows users to create, edit, delete, and view products. The application includes AJAX functionality for seamless interactions without page reloads.

## Features

-   Add new products with validation.
-   Edit existing products.
-   Delete products.
-   View a list of products sorted by creation date.
-   Display the total value of all products in the table footer.
-   Interactive toast notifications for success and error messages.

## Setup Instructions

### Prerequisites

-   PHP >= 8.2
-   Composer
-   Node.js and npm
-   A database (e.g., MySQL)

### Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/your-repo/coalition-test-v2.git
    cd coalition-test-v2
    ```

2. Install dependencies:

    ```bash
    composer install
    npm install
    ```

3. Set up the environment file:

    ```bash
    cp .env.example .env
    ```

    Update the `.env` file with your database credentials.

4. Generate the application key:

    ```bash
    php artisan key:generate
    ```

5. Run migrations:

    ```bash
    php artisan migrate
    ```

6. Start the development server:
    ```bash
    php artisan serve
    ```

## Usage

1. Navigate to the application in your browser (e.g., `http://127.0.0.1:8000`).
2. Use the form to add new products.
3. View the product list, edit or delete products using the respective buttons.
4. The total value of all products is displayed in the table footer.

## API Endpoints

-   `GET /api/products` - Fetch all products.
-   `POST /api/products` - Create a new product.
-   `PUT /api/products/{id}` - Update an existing product.
-   `DELETE /api/products/{id}` - Delete a product.

## Project Structure

-   **routes/web.php**: Defines web routes for the application.
-   **routes/api.php**: Defines api endpoints for the application.
-   **resources/views**: Contains Blade templates for the frontend.

-   **storage/app/products.json**: Contains the product data.

## License

This project is licensed under the MIT License.
