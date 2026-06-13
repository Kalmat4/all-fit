<script setup>
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
    calorie_deficit_mode: Boolean,
})

const calorieDeficitMode = ref(props.calorie_deficit_mode)

const toggleCalorieDeficitMode = () => {
    router.patch('/settings', {
        calorie_deficit_mode: calorieDeficitMode.value,
    }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Настройки сохранены'),
    })
}
</script>

<template>
    <AppLayout>

        <Head title="Настройки" />

        <h4 class="fw-bold mb-4">Настройки</h4>

        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="fw-semibold mb-1">Режим дефицита калорий</h6>
                    <p class="text-muted small mb-0">В каждом подходе оставляй 1-2 повтора в запасе</p>
                </div>
                <div class="form-check form-switch">
                    <input id="calorieDeficitMode" class="form-check-input" type="checkbox" role="switch"
                        style="width: 2.5em; height: 1.4em;" v-model="calorieDeficitMode"
                        @change="toggleCalorieDeficitMode" />
                </div>
            </div>
        </div>

    </AppLayout>
</template>
