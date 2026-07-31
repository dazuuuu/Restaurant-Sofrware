# Restaurant POS System

A comprehensive Point-of-Sale system built with PHP and SQLite designed for restaurants with multi-user roles, order management, and receipt printing.

## Features

### Core Features
- **Multi-User Authentication**: Separate login for admin/supervisors (password) and staff (PIN)
- **Role-Based Access Control**
  - Admin: Full system access
  - Supervisor: Manage users, menu, services, bookings
  - Cashier: Process payments and confirm orders
  - Waiter: Place orders and manage customer orders
  - Reception: Manage bookings and event reservations

### Menu Management
- Add, edit, delete food items and drinks
- Organize items by category
- Display prices and descriptions
- Upload item images (image URLs)

### Services Management
- Manage spa, massage, and accommodation services
- Track pricing and descriptions
- Categorize services

### Order Management
- Create orders by table number
- Add multiple items to orders
- Add special instructions per item
- Real-time order total calculation
- Order status tracking (Pending → Ready → Completed)

### Receipt Printing
- Automatic receipt generation for each order
- Two receipt types:
  - **Customer Receipt**: For the customer
  - **Kitchen Ticket**: For the kitchen staff
- Customizable receipt footer and restaurant info
- Print-ready text format

### Kitchen Dashboard
- View pending orders in real-time
- See order items with special instructions
- Mark orders as ready
- Track completed orders

### Booking Management
- Create event bookings
- Track deposits and attendee count
- Manage food and service selections
- Booking status management

### Settings
- Configure restaurant name
- Set currency (KSH, USD, etc.)
- Customize currency symbol
- Upload restaurant logo
- Customize receipt footer message

## User Roles & Login

### Admin/Supervisor Login
- **URL**: `http://localhost/POS/public/index.php?route=login-admin`
- **Credentials**: Email + Password
- **Default Admin**:
  - Email: `dazuhubs@gmail.com`
  - Password: `password123`

### Staff Login
- **URL**: `http://localhost/POS/public/index.php?route=login-staff`
- **Credentials**: User selection + PIN (4 digits)
- **PIN is created by Admin/Supervisor** when creating staff accounts

## Installation & Setup

1. **Navigate to project directory**:
   ```bash
   cd c:\Program Files\Ampps\www\POS
   ```

2. **Start PHP development server**:
   ```bash
   php -S localhost:8000 -t public
   ```

3. **Access the system**:
   - Admin: `http://localhost:8000/index.php?route=login-admin`
   - Staff: `http://localhost:8000/index.php?route=login-staff`

## Project Structure

```
POS/
├── apps/
│   ├── bootstrap.php              # Application initialization
│   ├── controllers/               # Request handlers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── UserController.php
│   │   ├── MenuController.php
│   │   ├── ServiceController.php
│   │   ├── BookingController.php
│   │   ├── OrderController.php
│   │   ├── SettingsController.php
│   │   └── KitchenController.php
│   ├── models/                    # Data models
│   │   ├── Database.php
│   │   ├── User.php
│   │   ├── MenuItem.php
│   │   ├── Service.php
│   │   ├── Booking.php
│   │   ├── Order.php
│   │   ├── Settings.php
│   │   └── Receipt.php
│   ├── services/                  # Business logic
│   │   ├── AuthService.php
│   │   ├── UserService.php
│   │   ├── MenuService.php
│   │   ├── ServiceService.php
│   │   ├── BookingService.php
│   │   ├── OrderService.php
│   │   └── SettingsService.php
│   ├── helpers/                   # Helper functions
│   │   └── Functions.php
│   └── views/                     # HTML templates
│       ├── layouts/               # Base layouts
│       ├── auth/                  # Login pages
│       ├── admin/                 # Admin dashboard & management
│       ├── waiter/                # Waiter order pages
│       ├── cashier/               # Cashier payment pages
│       ├── reception/             # Reception booking pages
│       └── kitchen/               # Kitchen preparation pages
├── public/
│   ├── index.php                  # Application entry point
│   ├── components/                # Frontend components
│   └── assets/                    # CSS, JS, images
├── storage/
│   └── pos.sqlite                 # SQLite database
└── README.md
```

## Workflow Examples

### Creating a Staff Account (Admin)
1. Login as Admin
2. Go to Users → Add User
3. Enter full name and email
4. Select role (Cashier, Waiter, Reception)
5. Enter PIN (4 digits)
6. Save

### Taking an Order (Waiter)
1. Login as Waiter with PIN
2. Click "New Order"
3. Enter table number
4. Add menu items with quantities
5. Add special instructions if needed
6. Print customer receipt
7. Send kitchen ticket to kitchen staff

### Processing Payment (Cashier)
1. Login as Cashier with PIN
2. View "Orders Ready for Payment"
3. Click "Confirm Payment"
4. Select payment method
5. Complete payment

### Preparing Order (Kitchen Staff)
1. Kitchen Dashboard shows all pending orders
2. View order details with special instructions
3. Prepare items
4. Click "Mark as Ready" when complete

## Database Tables

- `users` - Staff accounts with roles and PINs
- `menu_items` - Food items and drinks
- `services` - Spa, massage, accommodation services
- `orders` - Customer orders
- `order_items` - Items in each order
- `bookings` - Event bookings
- `receipts` - Printed receipts
- `settings` - System configuration

## Key URLs

| Page | URL | Role |
|------|-----|------|
| Admin Login | `/index.php?route=login-admin` | Admin/Supervisor |
| Staff Login | `/index.php?route=login-staff` | Cashier/Waiter/Reception |
| Dashboard | `/index.php?route=dashboard` | All |
| Users | `/index.php?route=users` | Admin/Supervisor |
| Menu | `/index.php?route=menu` | Admin/Supervisor |
| Services | `/index.php?route=services` | Admin/Supervisor |
| Orders | `/index.php?route=orders` | All |
| Kitchen | `/index.php?route=kitchen-dashboard` | Admin/Cashier |
| Bookings | `/index.php?route=bookings` | All |
| Settings | `/index.php?route=settings` | Admin |

## Technologies Used

- **Backend**: PHP 8.2+
- **Database**: SQLite3
- **Frontend**: Bootstrap 5.3, Bootstrap Icons
- **Authentication**: Password hashing (bcrypt) for admin, PIN for staff
- **Session Management**: PHP Sessions

## Security Features

- Role-based access control (RBAC)
- Password hashing with bcrypt
- PIN protection for staff accounts
- SQL injection prevention with prepared statements
- HTML escaping for output safety
- Session-based authentication

## Support

For issues or feature requests, please contact the development team.

## License

This project is proprietary and confidential.
