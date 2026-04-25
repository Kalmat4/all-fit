<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    sessions: Array,
})

const formatDuration = (minutes) => {
    if (!minutes) return null
    if (minutes < 60) return `${minutes} мин.`
    const h = Math.floor(minutes / 60)
    const m = minutes % 60
    return m > 0 ? `${h} ч. ${m} мин.` : `${h} ч.`
}

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1)
</script>

<template>
    <AppLayout>

        <Head title="История тренировок" />

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold">История тренировок</h4>
        </div>

        <div v-if="sessions.length" class="row g-3">
            <div v-for="session in sessions" :key="session.id" class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ session.program_name }}</div>
                            <div class="text-muted small mt-1">
                                {{ capitalize(session.day_of_week) }}, {{ session.completed_date }}
                                <span v-if="session.started_at">
                                    · {{ session.started_at }} – {{ session.completed_at }}
                                </span>
                                <span v-if="session.duration_minutes">
                                    · {{ formatDuration(session.duration_minutes) }}
                                </span>
                                <span class="ms-1">
                                    · {{ session.exercises_count }} упр.
                                </span>
                            </div>
                        </div>
                        <Link :href="`/history/${session.id}`" class="btn btn-outline-secondary btn-sm">
                            Подробнее
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                Завершённых тренировок пока нет
            </div>
        </div>

    </AppLayout>
</template>