<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    session: Object,
    exercises: Array,
})
</script>

<template>
    <AppLayout>

        <Head title="Детали тренировки" />

        <div class="d-flex align-items-center mb-4">
            <Link href="/history" class="btn btn-outline-secondary btn-sm me-3">← Назад</Link>
            <div>
                <h4 class="mb-0 fw-bold">{{ session.program_name }}</h4>
                <small class="text-muted">
                    {{ session.completed_at }}
                    <span v-if="session.duration_minutes"> · {{ session.duration_minutes }} мин.</span>
                </small>
            </div>
        </div>

        <div v-for="exercise in exercises" :key="exercise.id" class="card border-0 shadow-sm mb-3">
            <div class="card-body">

                <!-- Название + план -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="fw-bold mb-0">{{ exercise.exercise_name }}</h6>
                    <span class="text-muted small">
                        {{ exercise.planned_sets }} × {{ exercise.planned_reps }}
                        <span v-if="exercise.planned_weight"> · {{ exercise.planned_weight }} кг</span>
                    </span>
                </div>

                <!-- Подходы -->
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span v-for="set in exercise.sets" :key="set.set_number"
                        class="badge bg-light text-dark border px-3 py-2">
                        {{ set.set_number }}. {{ set.reps }} повт.
                        <span v-if="set.weight"> · {{ set.weight }} кг</span>
                    </span>
                </div>

                <!-- Комментарий -->
                <div v-if="exercise.comm" class="mt-2 text-muted small fst-italic">
                    💬 {{ exercise.comm }}
                </div>

            </div>
        </div>

    </AppLayout>
</template>