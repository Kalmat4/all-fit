# Project: All-Fit

## General Idea

All-Fit is a workout tracking application focused on:

- calisthenics (street workout)
- weight training (iron)
- tracking real performance (sets, reps, weight)
- simplicity and speed of use

The goal is NOT to build a complex fitness platform, but a clean and practical tool for logging workouts and tracking progress over time.

---

## Tech Stack

- Laravel (latest, v13+)
- PHP 8.3.25
- Node.js 22.22
- Composer 2.8.11
- Inertia.js
- Vue 3
- Bootstrap (no custom CSS)
- MySQL

---

## Architecture Decisions

- Monolith (no separate API)
- Backend + Frontend in one Laravel app
- Inertia used instead of REST API
- Bootstrap used instead of custom CSS
- Enums used instead of reference tables
- Business logic separated into Services
- Validation via FormRequest
- Thin controllers

---

## Authentication

- Laravel Fortify is used
- Only features enabled:
  - Login
  - Register
  - Logout

Disabled:
- password reset
- email verification
- two-factor auth
- profile update features

---

## Core MVP Features

### 1. Dashboard
- today's workout
- quick start workout
- navigation to workout programs

(no analytics, no progress yet)

---

### 2. Exercises
- list of exercises
- create/edit exercise
- categories and types via enums

---

### 3. Workout Programs
- predefined programs (system)
- user-created programs
- add exercises from library
- define sets/reps/weight
- reorder exercises

---

### 4. Workout Session (Active Training)
- start workout from program
- exercises copied into session (snapshot)
- input:
  - reps
  - weight
- show previous results
- complete workout

(no rest timer)

---

### 5. Workout History
- list of completed workouts
- view details

---

### 6. Progress
- NOT implemented yet

---

## Database Structure

### Core Tables

- users
- exercises
- workout_programs
- workout_program_exercises
- workout_sessions
- workout_session_exercises
- workout_session_sets

---

## Key Design Principle

Workout programs are templates.

Workout sessions are actual data.

History is stored independently from programs.

Programs can change — history must NOT break.

---

## Important Backend Structure

### Models
- User
- Exercise
- WorkoutProgram
- WorkoutProgramExercise
- WorkoutSession
- WorkoutSessionExercise
- WorkoutSessionSet

---

### Controllers
- DashboardController
- ExerciseController
- WorkoutProgramController
- WorkoutSessionController
- WorkoutHistoryController

---

### Services
- WorkoutProgramService
- WorkoutSessionService
- PreviousResultService

---

### Enums
- ExerciseCategoryEnum
- ExerciseTypeEnum
- WorkoutProgramTypeEnum
- WorkoutProgramLevelEnum
- WorkoutSessionStatusEnum

---

## Frontend Structure

### Pages
- Auth/Login.vue
- Auth/Register.vue
- Dashboard/Index.vue
- Exercises/*
- WorkoutPrograms/*
- WorkoutSessions/*
- WorkoutHistory/*

### Layouts
- AppLayout.vue
- AuthLayout.vue

### Components
- Navbar
- Flash messages
- UI helpers

---

## Development Approach

- Build step-by-step
- Start from auth
- Then:
  1. Exercises
  2. Programs
  3. Sessions
  4. History

No overengineering.

---

## Additional Notes

- No separate CSS files — only Bootstrap
- No premature optimization
- Focus on clean architecture and scalability
- Prefer clarity over magic