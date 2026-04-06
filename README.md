# LaraOrVite 🚀

[![Tests](https://github.com/LaraOrVite/framework/actions/workflows/tests.yml/badge.svg)](https://github.com/LaraOrVite/framework/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/laraorvite/framework.svg?style=flat-square)](https://packagist.org/packages/laraorvite/framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laraorvite/framework?v=1&style=flat-square&color=blue)](https://packagist.org/packages/laraorvite/framework)

**LaraOrVite** is a lightweight Laravel package designed to scaffold a modern frontend environment with **Vite** and a separate **API** architecture in seconds. It bridges the gap between Laravel's backend power and modern frontend frameworks like React, Vue, and Svelte.

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
