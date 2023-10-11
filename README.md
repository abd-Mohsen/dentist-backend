## Dentist app

a back-end for [this app](https://github.com/abdsyd/MediGear) and [dashboard](https://github.com/abd-Mohsen/dentist-dashboard).

- The backend primarily focuses on providing RESTful APIs that enable seamless communication between the mobile app, the dashboard and the server. These APIs facilitate various features, such as product listing, searching, ordering, and managing the inventory of dental equipment. Through the APIs, clients can interact with the backend to retrieve and update data in the MySQL database.

- Laravel Sanctum provides secure authentication mechanisms to ensure that only authorized users can access sensitive information and perform privileged actions. It handles user registration, login, and authentication, allowing dentists, suppliers, and administrators to have their respective roles and permissions within the app.

- Email OTP verification is supported for verifying accounts, and for resetting password.

- For the eCommerce aspect, the backend handles product management, including adding new products, updating their details, and managing their availability and pricing. It also facilitates the ordering process, handling the creation of orders, inventory deduction, and order status tracking.

- In addition to the eCommerce functionality, the backend incorporates a Warehouse Management System (WMS) to efficiently manage the inventory of dental equipment. It tracks stock levels, monitors incoming and outgoing shipments, and provides real-time updates on inventory availability. The WMS ensures accurate stock management, reducing the chances of overselling or running out of stock.

- The Laravel backend leverages the power of Laravel's Eloquent ORM capabilities, allowing for seamless database interactions and efficient query management. It ensures data consistency, reliability, and performance by implementing proper database design principles and utilizing Laravel's built-in features like migrations, seeders, and model relationships.

- used websockets for real-time notifications and chat.


