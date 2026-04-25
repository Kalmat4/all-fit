<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    programs: Array,
    types: Array,
    levels: Array,
})

const search = ref('')
const filterType = ref('')
const filterLevel = ref('')

const filtered = computed(() => {
    return props.programs.filter(p => {
        const matchName = p.name.toLowerCase().includes(search.value.toLowerCase())
        const matchType = filterType.value ? p.type === filterType.value : true
        const matchLevel = filterLevel.value ? p.level === filterLevel.value : true
        return matchName && matchType && matchLevel
    })
})

const destroy = (id) => {
    if (confirm('Удалить программу?')) {
        router.delete(`/workout-programs/${id}`)
    }
}
const startSession = (programId) => {
    router.post(`/sessions/start/${programId}`)
}
</script>

<template>
    <AppLayout>

        <Head title="Программы тренировок" />

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold">Программы тренировок</h4>
            <Link href="/workout-programs/create" class="btn btn-primary btn-sm">
                + Создать
            </Link>
        </div>

        <!-- Фильтры -->
        <div class="row g-2 mb-4">
            <div class="col-12 col-md-5">
                <input v-model="search" type="text" class="form-control" placeholder="Поиск по названию..." />
            </div>
            <div class="col-6 col-md-3">
                <select v-model="filterType" class="form-select">
                    <option value="">Все типы</option>
                    <option v-for="t in types" :key="t.value" :value="t.value">
                        {{ t.label }}
                    </option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select v-model="filterLevel" class="form-select">
                    <option value="">Все уровни</option>
                    <option v-for="l in levels" :key="l.value" :value="l.value">
                        {{ l.label }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Список -->
        <div v-if="filtered.length" class="row g-3">
            <div v-for="program in filtered" :key="program.id" class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ program.name }}</div>
                                <div class="mt-1">
                                    <span class="badge bg-secondary me-1">{{ program.typeLabel }}</span>
                                    <span class="badge bg-outline-secondary border text-secondary me-1">{{
                                        program.levelLabel }}</span>
                                    <span class="badge bg-light text-muted border">{{ program.exercises_count }}
                                        упр.</span>
                                </div>
                                <p v-if="program.description" class="text-muted small mt-2 mb-0">
                                    {{ program.description }}
                                </p>
                            </div>
                            <div class="ms-3 d-flex flex-column gap-2 align-items-end">
                                <button @click="startSession(program.id)" class="btn btn-primary btn-sm">
                                    ▶ Начать
                                </button>
                                <div v-if="program.can_edit" class="d-flex gap-1">
                                    <Link :href="`/workout-programs/${program.id}/edit`"
                                        class="btn btn-outline-secondary btn-sm">
                                        ✏️
                                    </Link>
                                    <button @click="destroy(program.id)" class="btn btn-outline-danger btn-sm">
                                        🗑️
                                    </button>
                                </div>
                                <span v-else class="badge bg-light text-muted border">Системная</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                Программы не найдены
            </div>
        </div>

    </AppLayout>
</template>