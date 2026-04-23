<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    exercise: Object,
    categories: Array,
    types: Array,
})

const form = useForm({
    name: props.exercise.name,
    category: props.exercise.category,
    type: props.exercise.type,
    description: props.exercise.description ?? '',
})

const submit = () => {
    form.put(`/exercises/${props.exercise.id}`)
}
</script>

<template>
    <AppLayout>

        <Head title="Редактировать упражнение" />

        <div class="d-flex align-items-center mb-4">
            <Link href="/exercises" class="btn btn-outline-secondary btn-sm me-3">← Назад</Link>
            <h4 class="mb-0 fw-bold">Редактировать упражнение</h4>
        </div>

        <div class="card border-0 shadow-sm" style="max-width: 560px;">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Название</label>
                    <input v-model="form.name" type="text" class="form-control"
                        :class="{ 'is-invalid': form.errors.name }" />
                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Категория</label>
                    <select v-model="form.category" class="form-select" :class="{ 'is-invalid': form.errors.category }">
                        <option v-for="c in categories" :key="c.value" :value="c.value">
                            {{ c.label }}
                        </option>
                    </select>
                    <div v-if="form.errors.category" class="invalid-feedback">{{ form.errors.category }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Тип</label>
                    <select v-model="form.type" class="form-select" :class="{ 'is-invalid': form.errors.type }">
                        <option v-for="t in types" :key="t.value" :value="t.value">
                            {{ t.label }}
                        </option>
                    </select>
                    <div v-if="form.errors.type" class="invalid-feedback">{{ form.errors.type }}</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Описание <span
                            class="text-muted fw-normal">(необязательно)</span></label>
                    <textarea v-model="form.description" class="form-control" rows="3" />
                </div>

                <button @click="submit" class="btn btn-primary w-100" :disabled="form.processing">
                    Сохранить изменения
                </button>
            </div>
        </div>

    </AppLayout>
</template>