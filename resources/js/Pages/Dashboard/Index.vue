<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    todaySession: Object,
    recentPrograms: Array,
    recentHistory: Array,
})

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1)

const startSession = (programId) => {
    router.post(`/sessions/start/${programId}`)
}
</script>

<template>
    <AppLayout>

        <Head title="Главная" />

        <!-- Текущая тренировка -->
        <div class="mb-4">
            <h5 class="text-muted text-uppercase fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 0.08em;">
                Текущая тренировка
            </h5>

            <div v-if="todaySession" class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ todaySession.program_name }}</div>
                        <small class="text-muted">В процессе</small>
                    </div>
                    <Link :href="`/sessions/${todaySession.id}`" class="btn btn-primary btn-sm">
                        Продолжить
                    </Link>
                </div>
            </div>

            <div v-else class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-3">За сегодня тренировок не было</p>
                    <Link href="/workout-programs" class="btn btn-primary">
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
                <div v-for="program in recentPrograms" :key="program.id" class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ program.name }}</div>
                                <small class="text-muted">{{ program.exercises_count }} упр.</small>
                            </div>
                            <button @click="startSession(program.id)" class="btn btn-outline-primary btn-sm">
                                ▶ Старт
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-3">Пока нет программ тренировок</p>
                    <Link href="/workout-programs/create" class="btn btn-outline-primary btn-sm">
                        Создать программу
                    </Link>
                </div>
            </div>
        </div>

        <!-- Последние тренировки -->
        <div class="mb-4">
            <h5 class="text-muted text-uppercase fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 0.08em;">
                Последние тренировки
            </h5>

            <div v-if="recentHistory.length" class="row g-2">
                <div v-for="session in recentHistory" :key="session.id" class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-semibold small">{{ session.program_name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    {{ capitalize(session.day_of_week) }}, {{ session.completed_at }}
                                </div>
                            </div>
                            <Link :href="`/history/${session.id}`" class="btn btn-outline-secondary btn-sm">
                                →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="card border-0 shadow-sm">
                <div class="card-body text-center py-3 text-muted small">
                    Завершённых тренировок пока нет
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
                    <Link href="/workout-programs" class="card border-0 shadow-sm text-decoration-none text-dark">
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
                    <Link href="/workout-programs/create"
                        class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body text-center py-3">
                            <div class="fs-4 mb-1">➕</div>
                            <div class="fw-semibold small">Новая программа</div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

    </AppLayout>
</template>