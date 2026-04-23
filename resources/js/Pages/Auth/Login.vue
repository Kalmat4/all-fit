<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post('/login')
}
</script>

<template>

    <Head title="All-Fit Авторизация" />

    <AuthLayout>
        <h1 class="h3 mb-4 text-center">Авторизация</h1>

        <form @submit.prevent="submit">
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

            <div class="form-check mb-3">
                <input id="remember" v-model="form.remember" type="checkbox" class="form-check-input">
                <label for="remember" class="form-check-label">Запомнить меня</label>
            </div>

            <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                Войти
            </button>
        </form>

        <p class="mt-3 mb-0 text-center">
            Нет аккаунта?
            <Link href="/register">Зарегистрируйтесь</Link>
        </p>
    </AuthLayout>
</template>