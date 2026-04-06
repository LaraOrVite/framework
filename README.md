# LaraOrVite 🚀

[![Tests](https://github.com/LaraOrVite/framework/actions/workflows/tests.yml/badge.svg)](https://github.com/LaraOrVite/framework/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/laraorvite/framework.svg?style=flat-square)](https://packagist.org/packages/laraorvite/framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laraorvite/framework?v=1&style=flat-square&color=blue)](https://packagist.org/packages/laraorvite/framework)

**LaraOrVite** is a lightweight Laravel package designed to scaffold a modern frontend environment with **Vite** and a separate **API** architecture in seconds. It bridges the gap between Laravel's backend power and modern frontend frameworks like React, Vue, and Svelte.

---
### 💻 Compatibility & Stack
![PHP](https://img.shields.io/badge/PHP-8.4%2B-777BB4?style=flat-square&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-10%2F11%2F12%2F13-FF2D20?style=flat-square&logo=laravel)
![Vite](https://img.shields.io/badge/Vite-9.0%2B-646CFF?style=flat-square&logo=vite)

**Supported Frontend Frameworks:**
![React](https://img.shields.io/badge/React-20232A?style=flat-square&logo=react)
![Vue.js](https://img.shields.io/badge/Vue.js-35495E?style=flat-square&logo=vuedotjs)
![Svelte](https://img.shields.io/badge/Svelte-FF3E00?style=flat-square&logo=svelte)
![TypeScript](https://img.shields.io/badge/TypeScript-007ACC?style=flat-square&logo=typescript)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript)

---

## ✨ Features

- 🛠 **One-Command Setup**: Set up your entire frontend and API scaffolding with a single Artisan command.
- 📦 **Framework Flexibility**: Supports React, Vue, Svelte, and Vanilla JS (both JS and TS versions).
- 🛡 **API Ready**: Automatically installs Laravel Sanctum and configures API routes if not already present.
- 📂 **Custom Structure**: Keep your frontend organized in a dedicated directory within `resources/`.
- ✅ **Tested & Secure**: Built with TDD principles and GitHub Actions for continuous integration.

---

## 🚀 Installation

You can install the package via composer:

```bash
composer require laraorvite/framework
````

## 🛠 Usage

After installing the package, run the setup command:

```bash
php artisan frontend:setup
```

### Optional: Custom Directory Name

By default, the frontend is created in `resources/frontend`. You can specify a custom name:

```bash
php artisan frontend:setup my-app
```

### What happens during setup?

1.  **API Scaffolding**: If you are on Laravel 11+, it runs `install:api` and sets up Sanctum.
2.  **Framework Choice**: You will be prompted to choose your preferred frontend framework (React, Vue, Svelte, etc.).
3.  **Vite Initialization**: It runs `npm create vite@latest` inside your resources folder automatically.
4.  **Routes Configuration**: It provides a pre-configured `api.php` stub to get you started.

-----

## 🏃‍♂️ Getting Started

Once the setup is complete, follow these steps to start developing:

1.  **Navigate to your frontend folder**:

    ```bash
    cd resources/frontend
    ```

2.  **Install dependencies & Start Vite**:

    ```bash
    npm install
    npm run dev
    ```

3.  **Start your Laravel server**:

    ```bash
    php artisan serve
    ```

-----

## 🧪 Running Tests

To run the package tests, use:

```bash
composer test
```

OR

```bash
./vendor/bin/phpunit
```

-----

## 🤝 Contributing

Contributions are welcome\! If you find a bug or have a feature request, please open an issue or submit a pull request.

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

-----

## 📄 License

The MIT License (MIT). Please see [License File](https://www.google.com/search?q=LICENSE) for more information.

-----

**Happy Coding with LaraOrVite\!** Created by [Niduranga Jayarathna](https://www.google.com/search?q=https://github.com/niduranga-jayarathna)

```
