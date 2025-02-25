# Wardrobe Management App

This is a Clothing Inventory Management application built with Laravel (backend) and Vue.js (frontend). The app allows users to add and update clothing items along with images.

## Setup Instructions

### Prerequisites
- PHP 8+
- Composer
- Node.js & npm
- Laravel 10+
- MySQL or SQLite

### Backend Setup
1. **Clone the Repository**
   ```sh
   git clone <repository-url>
   cd <repository-folder>
   ```
2. **Install Dependencies**
   ```sh
   composer install
   ```
3. **Set Up Environment**
   ```sh
   cp .env.example .env
   ```
   - Configure database credentials in `.env`.

4. **Generate Application Key**
   ```sh
   php artisan key:generate
   ```
5. **Run Migrations**
   ```sh
   php artisan migrate
   ```
6. **Serve the Application**
   ```sh
   php artisan serve
   ```

### Frontend Setup
1. **Navigate to the Frontend Directory**
   ```sh
   cd frontend
   ```
2. **Install Dependencies**
   ```sh
   npm install
   ```
3. **Run the Development Server**
   ```sh
   npm run dev
   ```

### Storage Setup for Images
1. **Create a Symbolic Link for Storage**
   ```sh
   php artisan storage:link
   ```
   This will ensure images stored in `storage/app/public` are accessible via `/storage/`.

### API Endpoints
- **GET /api/clothing** - Fetch all clothing items.
- **POST /api/clothing** - Add a new clothing item.
- **PUT /api/clothing/{id}** - Update an existing clothing item.

### Notes
- Ensure that `public/storage` is writable for storing images.
- If images do not load, clear cache using:
  ```sh
  php artisan cache:clear
  php artisan config:clear
  ```

