# ActiveCourt — Sports Field Booking System

ActiveCourt is a web-based sports field booking platform developed as a **group project for a Web Framework and Deployment course**. The project allows users to explore available sports fields, select schedules, make bookings, and go through a simulated online payment flow.

The project was built to explore full-stack web development using Laravel, communication between separate frontend and backend applications through APIs, and containerized deployment using Docker.

## Project Context

- **Type:** Group Project — Class Assignment
- **Team Size:** 5 members
- **Course:** Web Framework and Deployment
- **My Role:** Frontend & UI Contributor
- **Project Scope:** Academic / demonstration project

## My Contribution

My main contribution was focused on the **design and frontend experience** of ActiveCourt.

I worked particularly on the **field listing and booking interfaces**, helping translate the team's concept into a user-facing booking flow. I also participated in the early ideation and concept development of the project.

Beyond development, I contributed to preparing the project presentation and participated in presenting the final project with the team.

## Key Features

### Sports Field Discovery
Users can browse available sports facilities and view information about the fields before starting a booking.

### Field Booking
Users can select a field and available schedule and proceed through the booking flow.

### Booking History
Registered users can view their previous and current bookings along with their booking status.

### User Authentication
The application provides registration and login functionality for accessing user-specific features.

### Administration
The project includes administrative functionality for managing users, fields, schedules, and bookings.

### Payment Sandbox
The booking flow integrates **Midtrans Sandbox** to demonstrate how an external payment gateway can be connected to the application.

> The payment integration is intended for demonstration and academic purposes and should not be considered a production payment implementation.

## Tech Stack

### Application
- Laravel
- PHP
- Blade
- HTML
- CSS
- JavaScript

### Data & Communication
- MySQL
- REST API
- Laravel Middleware

### Deployment & Infrastructure
- Docker
- Docker Compose

### External Service
- Midtrans Sandbox

## Architecture

ActiveCourt separates the application into frontend and backend Laravel services.

```text
Browser
   |
   v
Frontend Laravel
   |
   | API Requests
   v
Backend Laravel
   |
   v
MySQL Database

Frontend / Backend
        |
        v
  Midtrans Sandbox
```

Docker Compose is used to run the frontend, backend, and MySQL services in a containerized development environment.

## Project Structure

```text
activecourt-booking-system/
├── backend/          # Backend Laravel application and API
├── frontend/         # User interface and frontend Laravel application
├── nginx/            # Web server configuration
├── docker-compose.yml
└── README.md
```

## Screenshots

### Field Discovery

_Add screenshot here._

### Booking Flow

_Add screenshot here._

### Midtrans Sandbox Payment

_Add screenshot here._

## Running the Project

### Requirements

Make sure the following are installed:

- Docker
- Docker Compose

### 1. Clone the repository

```bash
git clone https://github.com/AldoKT/activecourt-booking-system.git
cd activecourt-booking-system
```

### 2. Configure environment files

Create the environment files for both applications using their examples.

```bash
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
```

Configure the required database and external service environment variables before running the application.

### 3. Start the containers

```bash
docker compose up --build
```

By default, the project exposes:

- Frontend: `http://localhost:8000`
- Backend API: `http://localhost:8001`
- MySQL host port: `3307`

Additional Laravel setup such as application keys, migrations, or seeders may be required when running the project in a fresh environment.

## What I Learned

This project helped me understand how Laravel applications work beyond building individual pages.

Through ActiveCourt, I learned how a frontend application can communicate with a backend through APIs, how Laravel middleware can control and process application requests, and how different application services can work together.

I also gained my first practical experience using **Docker to containerize and run a multi-service web application**, which helped me better understand deployment and application environments.

Working in a five-person team also gave me experience turning shared ideas into an implemented interface and communicating the final result through a team presentation.

## Disclaimer

ActiveCourt was developed as an academic group project for learning and demonstration purposes. Some integrations, including the payment workflow, use sandbox environments and are not intended to represent a production-ready system.