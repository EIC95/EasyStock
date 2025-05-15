# EasyStock

EasyStock is a web application, I made for a school project, for inventory, order, and customer management, designed for small and medium-sized businesses. It enables efficient management of products, suppliers, categories, customers, orders, and delivery tracking.

## Main Features

- **Product Management**: Add, edit, delete, and view products with quantity, categories, suppliers, photos, etc.
- **Supplier Management**: Add, edit, delete, search, and filter suppliers.
- **Category Management**: Organize products by categories.
- **Customer Management**: List customers, search, and delete.
- **Order Management**: Create, track, update status, cancel, delete, and generate printable PDF invoices.
- **User Cart**: Add products to cart, validate orders, track ongoing and delivered orders.
- **Search and Filters**: Advanced search on products, suppliers, customers, and orders.
- **Admin Interface**: Dashboard, statistics, centralized management of all entities.

## Project Structure

- `admin/`: Admin interface and scripts (products, orders, suppliers management, etc.).
- `user/`: User interface for cart and order management.
- `uploads/`: Folder for product images.
- `verify.php`: Initialization and session verification file.
- `adminStyle.css`, `userStyle.css`: Stylesheets for admin and user interfaces.

## Requirements

- Web server
- PHP >= 7.4
- MySQL
- PDO

## Installation

1. Clone or copy the `EasyStock` folder into your web directory (e.g., `htdocs` with XAMPP).
2. Import the provided database (`database.sql`) into your MySQL server.
3. Configure the database connection in `connection.php`.
4. Start your server and access the application at `http://localhost/EasyStock`.

## Security

- Access is protected by sessions.
- User input is escaped to prevent XSS vulnerabilities.
- All queries use PDO prepared statements to prevent SQL injection.

