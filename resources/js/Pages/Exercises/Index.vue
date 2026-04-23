<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    exercises: Array,
    categories: Array,
    types: Array,
})

const search = ref('')
const filterCategory = ref('')
const filterType = ref('')

const filtered = computed(() => {
    return props.exercises.filter(e => {
        const matchName = e.name.toLowerCase().includes(search.value.toLowerCase())
        const matchCat = filterCategory.value ? e.category === filterCategory.value : true
        const matchType = filterType.value ? e.type === filterType.value : true
        return matchName && matchCat && matchType
    })
})

const destroy = (id) => {
    if (confirm('Удалить упражнение?')) {
        router.delete(`/exercises/${id}`)
    }
}
</script>

<template>
    <AppLayout>

        <Head title="Упражнения" />

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold">Упражнения</h4>
            <Link href="/exercises/create" class="btn btn-primary btn-sm">
                + Добавить
            </Link>
        </div>

        <!-- Фильтры -->
        <div class="row g-2 mb-4">
            <div class="col-12 col-md-5">
                <input v-model="search" type="text" class="form-control" placeholder="Поиск по названию..." />
            </div>
            <div class="col-6 col-md-3">
                <select v-model="filterCategory" class="form-select">
                    <option value="">Все категории</option>
                    <option v-for="c in categories" :key="c.value" :value="c.value">
                        {{ c.label }}
                    </option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select v-model="filterType" class="form-select">
                    <option value="">Все типы</option>
                    <option v-for="t in types" :key="t.value" :value="t.value">
                        {{ t.label }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Список -->
        <div v-if="filtered.length" class="row g-3">
            <div v-for="exercise in filtered" :key="exercise.id" class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">{{ exercise.name }}</div>
                                <div class="mt-1">
                                    <span class="badge bg-secondary me-1">{{ exercise.categoryLabel }}</span>
                                    <span class="badge bg-outline-secondary border text-secondary">{{ exercise.typeLabel
                                        }}</span>
                                </div>
                                <p v-if="exercise.description" class="text-muted small mt-2 mb-0">
                                    {{ exercise.description }}
                                </p>
                            </div>
                            <div v-if="exercise.can_edit" class="d-flex gap-2 ms-3">
                                <Link :href="`/exercises/${exercise.id}/edit`" class="btn btn-outline-secondary btn-sm">
                                    ✏️
                                </Link>
                                <button @click="destroy(exercise.id)" class="btn btn-outline-danger btn-sm">
                                    🗑️
                                </button>
                            </div>
                            <div v-else class="ms-3">
                                <span class="badge bg-light text-muted border">Системное</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                Упражнения не найдены
            </div>
        </div>

    </AppLayout>
</template>