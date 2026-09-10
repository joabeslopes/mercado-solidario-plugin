---
description: Architecture of my plugin mercado-solidario
alwaysApply: true
---

# Project Architecture
This is a Wordpress plugin. It creates pages and posts on the admin area of Wordpress. The main goal is to administrate an simple charity institution, with the structure of a physical market.
The project is split between backend and frontend.

## Frontend
- Vue 3 with composition API.
- Javascript.
- NPM for dependency management.
- Vite as build system.
- Frontend code in folder `frontend`.
- Vue JS components in `frontend/src/components`.
- Project javascript libraries in `frontend/src/js`.
- Functions for fetching php backend in `frontend/src/js/myApiClient.js`.
- Global css files in `frontend/src/css`.
- Vue js main pages in `frontend/src/pages`, with each page inside a subfolder.
- Builds in `frontend/dist`.

## Frontend Coding Standards
- Naming convention is `camelCase`.
- On vue components, never use Options API, always use composition API with `<script setup>`.
- Prefer use of PrimeVue components instead of raw html.

## Backend
- Wordpress with PHP 8.
- Main plugin file in `mercado-solidario.php`.
- PHP backend base folder in `class`.
- `class/Controller` contains controller classes that handle the business logic, process API requests and coordinate between models and views.
- `class/Model` contains model classes that interact with WordPress data
Handle database operations and data manipulation.
- `class/Pages` contains page management classes. Handle the creation and management of WordPress admin pages.
- `class/REST` contains REST API route definitions. Register WordPress REST API endpoints.
- `class/Security` contains security-related classes. Handle capabilities management.
- `class/Base` contains base classes for inheritance. Abstract base classes for common functionality.
- The backend creates routes in Wordpress REST API.
- The REST request flow is Router > Controller > Model, with body and response in json.
- The capabilities of each page are defined in the class `CapabilitiesManager`.
- A post type is created by the corresponding controller class.
- The controllers are initialized in the Router class, not directly in the main plugin file.

## PHP Coding Standards
- Use classes and object orientation.
- Use composer.
- Use namespaces on the PSR-4 standard.
- Separate frontend and backend logic.
- Naming convention is `snake_case`.
- Use wordpress post system insted of creating new tables.

# External guides

The plugin is created based on:
- [Wordpress plugin documentation](https://developer.wordpress.org/plugins/).
- [Vue documentation](https://doc.vueframework.com/guide/introduction.html).
- [Vue Router documentation](https://router.vuejs.org/guide/).
- [PrimeVue Documentation](https://primevue.org/introduction/).