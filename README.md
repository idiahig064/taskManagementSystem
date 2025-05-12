# Task Management System

The **Task Management System** is a robust web application built with Laravel, providing all the necessary tools to create, categorize, track, and manage tasks for individuals and teams. It includes advanced features such as user authentication, file attachments, reporting, soft deletes ("trash"), and a modern, responsive interface using Tailwind CSS and Alpine.js.

---

## Features

### 📝 Task Management

- **Create and Edit Tasks:** Add tasks with titles, descriptions, due dates, priorities, and categories.
- **Status Tracking:** Mark tasks as completed or incomplete.
- **Task Attachments:** Upload and manage files attached to tasks (documents, images, etc).
- **Prioritization:** Assign priority levels (Low, Medium, High, Urgent).

### 📂 Categories

- **Organize Tasks:** Group tasks by custom categories for better organization.
- **CRUD Operations:** Create, edit, and remove categories.

### 🚮 Trash Management

- **Soft Delete:** Move tasks to trash instead of permanently deleting them.
- **Restore or Permanent Delete:** Restore trashed tasks or remove them permanently.

### 📊 Reporting

- **Overview Dashboard:** Visual summary of your tasks (completed, pending, by category, etc).
- **Activity Reports:** Track progress and productivity over time.

### 👤 User Management

- **Authentication:** Secure registration, login, password reset.
- **Profile Management:** Update user information and passwords.

### 💡 UI & Experience

- **Responsive Design:** Works seamlessly on desktop and mobile browsers.
- **Fast Frontend:** Powered by Vite, Tailwind CSS, and Alpine.js for a smooth UX.

---

## Getting Started

### Prerequisites

- **PHP** >= 8.2
- **Composer** (dependency management for PHP)
- **Node.js & npm** (JavaScript tooling)
- **Database:** MySQL, PostgreSQL, or SQLite

### Installation

1. **Clone the repository**
   ```shell
   git clone https://github.com/idiahig064/taskManagementSystem.git
   cd taskManagementSystem
   ```

2. **Install PHP dependencies**
   ```shell
   composer install
   ```

3. **Install JavaScript dependencies**
   ```shell
   npm install
   ```

4. **Environment configuration**
   - Copy the template and configure your environment:
     ```shell
     cp .env.example .env
     ```
   - Edit `.env` with your database and mail settings.

5. **Generate application key**
   ```shell
   php artisan key:generate
   ```

6. **Run database migrations and seeders**
   ```shell
   php artisan migrate --seed
   ```
   *(Includes default users, categories, and sample tasks for testing.)*

7. **Compile frontend assets**
   - For production:
     ```shell
     npm run build
     ```
   - For development (with hot reloading):
     ```shell
     npm run dev
     ```

8. **Start Laravel development server**
   ```shell
   php artisan serve
   ```
   The application will be available at [http://localhost:8000](http://localhost:8000).

---

## Usage

- **Register an account** or **log in** with your credentials.
- **Create categories** to organize your tasks.
- **Add new tasks** and assign them to categories, specify due dates, and attach files if needed.
- **Mark tasks as complete** when finished.
- **View your dashboard** for a summary of your tasks and activity.
- **Move tasks to trash** if no longer needed, and restore or permanently delete as desired.
- **Access your profile** to update your personal info and password.

---

## Project Structure

- `/app/Http/Controllers/` – Laravel controllers (Tasks, Categories, Reporting, Auth, etc.)
- `/app/Models/` – Eloquent models
- `/resources/views/` – Blade templates for all pages (dashboard, tasks, categories, profile, etc.)
- `/routes/web.php` – Main application routes
- `/database/` – Migrations, factories, and seeders
- `/public/` – Entry point, static files, compiled assets
- `/resources/js/`, `/resources/css/` – Frontend source code

---

## Technology Stack

- **Framework:** Laravel
- **Frontend:** Tailwind CSS, Alpine.js, Vite
- **Testing:** PHPUnit, Pest
- **Database:** Eloquent ORM (MySQL, PostgreSQL, SQLite supported)
- **Authentication:** Laravel Breeze/Jetstream (or equivalent)

---

## License

This project is licensed under the [MIT License](LICENSE).