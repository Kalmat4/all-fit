<script setup>
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
    session: Object,
    exercises: Array,
    calorie_deficit_mode: Boolean,
})

// Для каждого упражнения храним локальный черновик подходов
const drafts = ref({})
const commDrafts = ref({})

// Режим "По цели повторений": сохранённые подходы и черновик нового подхода
const targetSets = ref({})
const targetDrafts = ref({})
const showTargetInput = ref({})

// Разминка: отметка выполнения хранится только локально
const warmupDone = ref({})

props.exercises.forEach(ex => {

    if (ex.is_warmup) {
        warmupDone.value[ex.id] = false
        return
    }

    commDrafts.value[ex.id] = ex.comm;

    if (ex.mode === 'sets') {
        drafts.value[ex.id] = [];
        for (let i = 1; i <= ex.planned_sets; i++) {
            const existing = ex.sets.find(s => s.set_number === i)
            drafts.value[ex.id].push({
                set_number: i,
                reps: existing?.reps ?? '',
                weight: existing?.weight ?? ex.planned_weight ?? '',
                saved: !!existing?.completed_at
            })
        }
    } else {
        targetSets.value[ex.id] = ex.sets.map(s => ({
            set_number: s.set_number,
            reps: s.reps,
            weight: s.weight,
        }))
        targetDrafts.value[ex.id] = { reps: '', weight: ex.planned_weight ?? '' }
        showTargetInput.value[ex.id] = false
    }
})

const saving = ref({})
const savingTarget = ref({})

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

// Сумма повторений по упражнению с режимом "По цели повторений"
const targetTotal = (exerciseId) => {
    return targetSets.value[exerciseId].reduce((sum, s) => sum + (parseInt(s.reps) || 0), 0)
}

const targetProgress = (exercise) => {
    return Math.min(100, Math.round((targetTotal(exercise.id) / exercise.target_reps) * 100))
}

const isTargetDone = (exercise) => {
    return targetTotal(exercise.id) >= exercise.target_reps
}

const addTargetSet = async (exercise) => {
    const draft = targetDrafts.value[exercise.id]
    if (!draft.reps) return

    const setNumber = targetSets.value[exercise.id].length + 1
    savingTarget.value[exercise.id] = true

    try {
        await fetch(`/sessions/${exercise.id}/set`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                set_number: setNumber,
                reps: draft.reps,
                weight: draft.weight || null,
            }),
        })

        targetSets.value[exercise.id].push({
            set_number: setNumber,
            reps: draft.reps,
            weight: draft.weight || null,
        })

        targetDrafts.value[exercise.id] = { reps: '', weight: exercise.planned_weight ?? '' }
        showTargetInput.value[exercise.id] = false
    } finally {
        savingTarget.value[exercise.id] = false
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

        <!-- Режим дефицита калорий -->
        <div v-if="calorie_deficit_mode" class="alert alert-warning">
            ⚠️ Режим дефицита калорий: в каждом подходе оставляй 1-2 повтора в запасе. Не иди на максимум.
        </div>

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
        <template v-for="exercise in exercises" :key="exercise.id">

            <!-- Разминка -->
            <div v-if="exercise.is_warmup" class="card border-0 shadow-sm mb-3 bg-light border border-warning">
                <div class="card-body text-center">
                    <h6 class="fw-bold mb-3">Разминка 🔥</h6>
                    <button v-if="!warmupDone[exercise.id]" @click="warmupDone[exercise.id] = true"
                        class="btn btn-warning btn-lg w-100">
                        ✓ Разминка выполнена
                    </button>
                    <button v-else class="btn btn-success btn-lg w-100" disabled>
                        Выполнено
                    </button>
                </div>
            </div>

            <!-- Обычное упражнение -->
            <div v-else class="card border-0 shadow-sm mb-3">
            <div class="card-body">

                <!-- Название упражнения -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="fw-bold mb-0">{{ exercise.exercise_name }}</h6>
                    <span class="text-muted small">
                        <template v-if="exercise.mode === 'sets'">
                            {{ exercise.planned_sets }} × {{ exercise.planned_reps }}
                        </template>
                        <template v-else>
                            Цель: {{ exercise.target_reps }} повторений
                        </template>
                        <span v-if="exercise.planned_weight"> · {{ exercise.planned_weight }} кг</span>
                    </span>
                </div>

                <!-- Предыдущий результат -->
                <div v-if="exercise.mode === 'sets' && exercise.previous.sets?.length" class="mb-3 p-2 bg-light rounded">
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
                <div v-else-if="exercise.mode === 'target_reps' && exercise.previous.summary"
                    class="mb-3 p-2 bg-light rounded">
                    <div class="text-muted mb-1"
                        style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        Прошлый раз
                    </div>
                    <div class="small">{{ exercise.previous.summary }}</div>
                </div>

                <!-- Режим "По подходам" -->
                <template v-if="exercise.mode === 'sets'">
                    <div v-for="(set, index) in drafts[exercise.id]" :key="index"
                        class="row g-2 align-items-center mb-2">
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
                </template>

                <!-- Режим "По цели повторений" -->
                <template v-else>
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <span class="small fw-semibold">
                            {{ targetTotal(exercise.id) }} / {{ exercise.target_reps }} повторений
                        </span>
                        <span v-if="isTargetDone(exercise)" class="badge bg-success">✓ Выполнено!</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            :style="{ width: targetProgress(exercise) + '%' }"
                            :aria-valuenow="targetTotal(exercise.id)" aria-valuemin="0"
                            :aria-valuemax="exercise.target_reps">
                        </div>
                    </div>

                    <div v-if="targetSets[exercise.id].length" class="d-flex flex-wrap gap-2 mb-3">
                        <span v-for="(set, index) in targetSets[exercise.id]" :key="index"
                            class="badge bg-light text-dark border">
                            {{ index + 1 }}) {{ set.reps }} повт.
                            <span v-if="set.weight">· {{ set.weight }} кг</span>
                        </span>
                    </div>

                    <div v-if="!isTargetDone(exercise)" class="mb-2">
                        <div v-if="showTargetInput[exercise.id]" class="row g-2 align-items-center">
                            <div class="col">
                                <input v-model="targetDrafts[exercise.id].reps" type="number" min="1"
                                    class="form-control form-control-sm" placeholder="Повторения" />
                            </div>
                            <div class="col">
                                <input v-model="targetDrafts[exercise.id].weight" type="number" step="0.5" min="0"
                                    class="form-control form-control-sm" placeholder="Вес (кг)" />
                            </div>
                            <div class="col-auto">
                                <button @click="addTargetSet(exercise)" class="btn btn-outline-primary btn-sm"
                                    :disabled="savingTarget[exercise.id]">
                                    ✓
                                </button>
                            </div>
                        </div>
                        <button v-else @click="showTargetInput[exercise.id] = true"
                            class="btn btn-outline-primary btn-sm">
                            + Добавить подход
                        </button>
                    </div>
                </template>

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

        </template>

    </AppLayout>
</template>
