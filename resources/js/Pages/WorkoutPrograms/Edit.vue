<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    program: Object,
    exercises: Array,
    types: Array,
    levels: Array,
})

const form = useForm({
    name: props.program.name,
    description: props.program.description ?? '',
    type: props.program.type,
    level: props.program.level,
    exercises: props.program.exercises.map(e => ({
        exercise_id: e.exercise_id,
        exercise_name: e.exercise_name,
        sets: e.sets,
        reps: e.reps,
        weight: e.weight,
    })),
})

const exerciseSearch = ref('')
const filteredExercises = computed(() => {
    if (!exerciseSearch.value) return props.exercises
    return props.exercises.filter(e =>
        e.name.toLowerCase().includes(exerciseSearch.value.toLowerCase())
    )
})

const addExercise = (exercise) => {
    const already = form.exercises.find(e => e.exercise_id === exercise.id)
    if (already) return
    form.exercises.push({
        exercise_id: exercise.id,
        exercise_name: exercise.name,
        sets: 3,
        reps: '10',
        weight: null,
    })
}

const removeExercise = (index) => form.exercises.splice(index, 1)

const moveUp = (index) => {
    if (index === 0) return
    const arr = form.exercises
        ;[arr[index - 1], arr[index]] = [arr[index], arr[index - 1]]
}

const moveDown = (index) => {
    if (index === form.exercises.length - 1) return
    const arr = form.exercises
        ;[arr[index], arr[index + 1]] = [arr[index + 1], arr[index]]
}

const submit = () => {
    form.put(`/workout-programs/${props.program.id}`)
}
</script>

<template>
    <AppLayout>

        <Head title="Редактировать программу" />

        <div class="d-flex align-items-center mb-4">
            <Link href="/workout-programs" class="btn btn-outline-secondary btn-sm me-3">← Назад</Link>
            <h4 class="mb-0 fw-bold">Редактировать программу</h4>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Основное</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Название</label>
                            <input v-model="form.name" type="text" class="form-control"
                                :class="{ 'is-invalid': form.errors.name }" />
                            <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Тип</label>
                            <select v-model="form.type" class="form-select" :class="{ 'is-invalid': form.errors.type }">
                                <option v-for="t in types" :key="t.value" :value="t.value">{{ t.label }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Уровень</label>
                            <select v-model="form.level" class="form-select"
                                :class="{ 'is-invalid': form.errors.level }">
                                <option v-for="l in levels" :key="l.value" :value="l.value">{{ l.label }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Описание <span
                                    class="text-muted fw-normal">(необязательно)</span></label>
                            <textarea v-model="form.description" class="form-control" rows="3" />
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Добавить упражнение</h6>
                        <input v-model="exerciseSearch" type="text" class="form-control form-control-sm mb-2"
                            placeholder="Поиск..." />
                        <div style="max-height: 260px; overflow-y: auto;">
                            <div v-for="exercise in filteredExercises" :key="exercise.id"
                                class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <div class="small fw-semibold">{{ exercise.name }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ exercise.categoryLabel }} · {{
                                        exercise.typeLabel }}</div>
                                </div>
                                <button @click="addExercise(exercise)" class="btn btn-outline-primary btn-sm">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Упражнения программы</h6>
                        <div v-if="!form.exercises.length" class="text-center text-muted py-4">
                            Добавьте упражнения из списка слева
                        </div>
                        <div v-for="(item, index) in form.exercises" :key="index" class="card border mb-2">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold small">{{ item.exercise_name }}</span>
                                    <div class="d-flex gap-1">
                                        <button @click="moveUp(index)"
                                            class="btn btn-outline-secondary btn-sm py-0">↑</button>
                                        <button @click="moveDown(index)"
                                            class="btn btn-outline-secondary btn-sm py-0">↓</button>
                                        <button @click="removeExercise(index)"
                                            class="btn btn-outline-danger btn-sm py-0">✕</button>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label class="form-label mb-1" style="font-size:0.7rem;">Подходы</label>
                                        <input v-model.number="item.sets" type="number" min="1" max="20"
                                            class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label mb-1" style="font-size:0.7rem;">Повторения</label>
                                        <input v-model="item.reps" type="text"
                                            class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label mb-1" style="font-size:0.7rem;">Вес (кг)</label>
                                        <input v-model.number="item.weight" type="number" min="0" step="0.5"
                                            class="form-control form-control-sm" placeholder="—" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button @click="submit" class="btn btn-primary w-100 mt-3" :disabled="form.processing">
                    Сохранить изменения
                </button>
            </div>
        </div>
    </AppLayout>
</template>