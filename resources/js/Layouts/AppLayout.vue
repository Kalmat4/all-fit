<script setup>
import { Link, router } from '@inertiajs/vue3'

const logout = () => {
    router.post('/logout')
}
</script>

<template>
    <div class="min-vh-100 bg-light">

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <Link class="navbar-brand fw-bold" href="/dashboard">
                    💪 All-Fit
                </Link>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNav"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <Link class="nav-link" href="/dashboard">Главная</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/exercises">Упражнения</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/workout-programs">Программы тренировок</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/history">История</Link>
                        </li>
                    </ul>

                    <button class="btn btn-outline-light btn-sm" @click="logout">
                        Выйти
                    </button>
                </div>
            </div>
        </nav>

        <!-- Page content -->
        <main class="container py-4">
            <slot />
        </main>

    </div>
</template>