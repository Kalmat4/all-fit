<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    todaySession: Object,       // null или активная сессия
    recentPrograms: Array,      // [] или список программ
})
</script>

<template>
    <AppLayout>
        <Head title="Dashboard" />

        <!-- Сегодняшняя тренировка -->
        <div class="mb-4">
            <h5 class="text-muted text-uppercase fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 0.08em;">
                Сегодня
            </h5>

            <div v-if="todaySession" class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ todaySession.program_name }}</div>
                        <small class="text-muted">В процессе</small>
                    </div>
                    <Link
                        :href="`/sessions/${todaySession.id}`"
                        class="btn btn-primary btn-sm"
                    >
                        Продолжить
                    </Link>
                </div>
            </div>

            <div v-else class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-3">За сегодня не было тренировок</p>
                    <Link href="/programs" class="btn btn-primary">
                        Начать тренировку
                    </Link>
                </div>
            </div>
        </div>

        <!-- Быстрый старт -->
        <div class="mb-4">
            <h5 class="text-muted text-uppercase fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 0.08em;">
                Быстрый старт
            </h5>

            <div v-if="recentPrograms.length" class="row g-3">
                <div
                    v-for="program in recentPrograms"
                    :key="program.id"
                    class="col-12 col-md-4"
                >
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ program.name }}</div>
                                <small class="text-muted">{{ program.exercises_count }} exercises</small>
                            </div>
                            <Link
                                :href="`/sessions/start/${program.id}`"
                                class="btn btn-outline-primary btn-sm"
                            >
                                Старт
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-3">Пока нет программ тренировок.</p>
                    <Link href="/programs/create" class="btn btn-outline-primary btn-sm">
                        Создать программу
                    </Link>
                </div>
            </div>
        </div>

        <!-- Навигация -->
        <div>
            <h5 class="text-muted text-uppercase fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 0.08em;">
                Навигация
            </h5>

            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <Link href="/programs" class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body text-center py-3">
                            <div class="fs-4 mb-1">📋</div>
                            <div class="fw-semibold small">Программы тренировок</div>
                        </div>
                    </Link>
                </div>
                <div class="col-6 col-md-3">
                    <Link href="/exercises" class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body text-center py-3">
                            <div class="fs-4 mb-1">🏋️</div>
                            <div class="fw-semibold small">Упражнения</div>
                        </div>
                    </Link>
                </div>
                <div class="col-6 col-md-3">
                    <Link href="/history" class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body text-center py-3">
                            <div class="fs-4 mb-1">📅</div>
                            <div class="fw-semibold small">История</div>
                        </div>
                    </Link>
                </div>
                <div class="col-6 col-md-3">
                    <Link href="/programs/create" class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body text-center py-3">
                            <div class="fs-4 mb-1">➕</div>
                            <div class="fw-semibold small">Новая программа тренировок</div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

    </AppLayout>
</template>