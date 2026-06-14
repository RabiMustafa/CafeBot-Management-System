# CafeBot: Full-Stack Restaurant Management System

[Live Demo](http://cafebot.site.je)

CafeBot is a complete web-based restaurant management system built with PHP and MySQL. It moves beyond a simple digital menu by implementing Role-Based Access Control (RBAC) to handle the distinct workflows of customers, waitstaff, and administrators within a single integrated environment.

## Key Features

* **Role-Based Access Control (RBAC):** Secure routing and session management that dynamically alters the UI and permissions based on three distinct user roles (Customer, Waiter, Admin).
* **Staff Order Claiming System:** A real-time dashboard for waitstaff to view pending orders, claim them, and update statuses, which immediately reflects on the customer's active order view via relational database joins.
* **Admin Analytics & Management:** A secure admin portal to view complete order histories (with dynamic table filtering) and manage employee roles directly from the UI.
* **Simulated OTP Verification:** A portfolio-safe implementation of email verification. It enforces a strict database state (`is_verified = 0`) upon registration and redirects unauthorized users to a verification portal, simulating an email OTP flow without requiring SMTP configuration.

## Tech Stack

* **Backend:** PHP (Vanilla)
* **Database:** MySQL / phpMyAdmin (Relational schema with foreign key constraints)
* **Frontend:** HTML5, CSS3, JavaScript
* **Deployment:** InfinityFree (Live Hosting), GitHub (Version Control)

## Local Setup Instructions

To run this project locally on your machine using XAMPP or a similar environment:

1. **Clone the repository:**
   `git clone https://github.com/your-username/CafeBot.git`
2. **Setup the Database:**
   * Open phpMyAdmin (`localhost/phpmyadmin`).
   * Create a new database named `cafe`.
   * Import the provided `cafe_db.sql` file to build the schema and populate dummy data.
3. **Configure Credentials:**
   * Open `db.php` in the root directory.
   * Update the database credentials to match your local setup (usually `user = "root"` and `password = ""`).
4. **Run:**
   * Place the project folder inside your `htdocs` directory.
   * Navigate to `http://localhost/CafeBot` in your browser.

---
*Developed as a portfolio showcase demonstrating backend architecture, database normalization, and secure session management.*
