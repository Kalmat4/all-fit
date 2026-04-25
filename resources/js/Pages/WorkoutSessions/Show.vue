<script setup>
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
    session: Object,
    exercises: Array,
})

// Для каждого упражнения храним локальный черновик подходов
const drafts = ref({})
const commDrafts = ref({})

props.exercises.forEach(ex => {


    drafts.value[ex.id] = [];
    commDrafts.value[ex.id] = ex.comm;
    for (let i = 1; i <= ex.planned_sets; i++) {
        const existing = ex.sets.find(s => s.set_number === i)
        drafts.value[ex.id].push({
            set_number: i,
            reps: existing?.reps ?? '',
            weight: existing?.weight ?? ex.planned_weight ?? '',
            saved: !!existing?.completed_at
        })
    }
})

const saving = ref({})

const saveSet = async (exerciseId, sessionExerciseId, setIndex) => {
    const set = drafts.value[exerciseId][setIndex]
    const key = `${exerciseId}-${setIndex}`
    saving.value[key] = true

    try {
        await fetch(`/sessions/${sessionExerciseId}/set`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                set_number: set.set_number,
                reps: set.reps,
                weight: set.weight || null,
            }),
        })
        set.saved = true
    } finally {
        saving.value[key] = false
        toast.success('Подход сохранён!');
    }
}


const saveComm = async (exerciseId) => {
    const drComm = commDrafts.value[exerciseId];

    try {
        await fetch(`/sessions/${exerciseId}/comm`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                comm: drComm
            }),
        })
    } finally {
        toast.success('Комментарий к подходу сохранён');
    }
}

const complete = () => {
    if (confirm('Завершить тренировку?')) {
        router.post(`/sessions/${props.session.id}/complete`)
    }
}
</script>

<template>
    <AppLayout>

        <Head title="Тренировка" />

        <!-- Шапка -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 fw-bold">{{ session.program_name }}</h4>
                <small class="text-muted">В процессе</small>
            </div>
            <button @click="complete" class="btn btn-success">
                ✓ Завершить
            </button>
        </div>

        <!-- Упражнения -->
        <div v-for="exercise in exercises" :key="exercise.id" class="card border-0 shadow-sm mb-3">
            <div class="card-body">

                <!-- Название упражнения -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="fw-bold mb-0">{{ exercise.exercise_name }}</h6>
                    <span class="text-muted small">
                        {{ exercise.planned_sets }} × {{ exercise.planned_reps }}
                        <span v-if="exercise.planned_weight"> · {{ exercise.planned_weight }} кг</span>
                    </span>
                </div>

                <!-- Предыдущий результат -->
                <div v-if="exercise.previous.sets?.length" class="mb-3 p-2 bg-light rounded">
                    <div class="text-muted mb-1"
                        style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        Прошлый раз
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span v-for="prev in exercise.previous.sets" :key="prev.set_number"
                            class="badge bg-light text-dark border">
                            {{ prev.set_number }}) {{ prev.reps }} повт.
                            <span v-if="prev.weight">· {{ prev.weight }} кг</span>
                        </span>
                    </div>
                </div>

                <!-- Подходы -->
                <div v-for="(set, index) in drafts[exercise.id]" :key="index" class="row g-2 align-items-center mb-2">
                    <div class="col-auto">
                        <span class="badge rounded-circle d-flex align-items-center justify-content-center"
                            :class="set.saved ? 'bg-success' : 'bg-secondary'"
                            style="width: 28px; height: 28px; font-size: 0.75rem;">
                            {{ set.set_number }}
                        </span>
                    </div>
                    <div class="col">
                        <input v-model="set.reps" type="text" class="form-control form-control-sm"
                            placeholder="Повторения" :disabled="set.saved" />
                    </div>
                    <div class="col">
                        <input v-model="set.weight" type="number" step="0.5" min="0"
                            class="form-control form-control-sm" placeholder="Вес (кг)" :disabled="set.saved" />
                    </div>
                    <div class="col-auto">
                        <button v-if="!set.saved" @click="saveSet(exercise.id, exercise.id, index)"
                            class="btn btn-outline-primary btn-sm" :disabled="saving[`${exercise.id}-${index}`]">
                            ✓
                        </button>
                        <span v-else class="text-success">✓</span>
                    </div>
                </div>

                <!-- Предыдущий комментарий -->
                <div v-if="exercise.previous.comm?.length" class="mb-3 p-2 bg-light rounded">
                    <div class="text-muted mb-1"
                        style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        Прошлый комментарий
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border">
                            {{ exercise.previous.comm }}
                        </span>
                    </div>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="badge d-flex align-items-center justify-content-center bg-secondary">
                            Комментарий
                        </span>
                    </div>
                    <div class="col">
                        <textarea name="exerciseComm{{ exercise.id }}" id="exerciseComm{{ exercise.id }}"
                            class="form-control from-control-sm" v-model="commDrafts[exercise.id]"
                            placeholder="Введите комментарий (необязательно)"></textarea>
                    </div>
                    <div class="col-auto">
                        <button @click="saveComm(exercise.id)" class="btn btn-outline-primary btn-sm">
                            ✓
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </AppLayout>
</template>