<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post('/register')
}
</script>

<template>

    <Head title="All-Fit Регистрация" />

    <AuthLayout>
        <h1 class="h3 mb-4 text-center">Регистрация</h1>

        <form @submit.prevent="submit">
            <div class="mb-3">
                <label class="form-label">Имя</label>
                <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }">
                <div v-if="form.errors.name" class="invalid-feedback">
                    {{ form.errors.name }}
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" class="form-control"
                    :class="{ 'is-invalid': form.errors.email }">
                <div v-if="form.errors.email" class="invalid-feedback">
                    {{ form.errors.email }}
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <input v-model="form.password" type="password" class="form-control"
                    :class="{ 'is-invalid': form.errors.password }">
                <div v-if="form.errors.password" class="invalid-feedback">
                    {{ form.errors.password }}
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Подтверждение пароля</label>
                <input v-model="form.password_confirmation" type="password" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                Создать аккаунт
            </button>
        </form>

        <p class="mt-3 mb-0 text-center">
            Уже есть аккаунт?
            <Link href="/login">Войти</Link>
        </p>
    </AuthLayout>
</template>