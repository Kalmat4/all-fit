<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
    user: Object,
})

const page = usePage()

const profileForm = useForm({
    name:  props.user.name,
    email: props.user.email,
})

const passwordForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
})

const saveProfile = () => {
    profileForm.patch('/profile', { preserveScroll: true })
}

const savePassword = () => {
    passwordForm.patch('/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    })
}

const logout = () => {
    router.post('/logout')
}
</script>

<template>
    <AppLayout>
        <Head title="Профиль — AgriFireShield" />

        <!-- Header -->
        <header class="afs-header">
            <div class="afs-header__inner">
                <Link href="/dashboard" class="afs-logo">
                    <span class="afs-logo__icon">🔥</span>
                    <span class="afs-logo__text">AgriFireShield</span>
                </Link>
                <nav class="afs-nav">
                    <Link href="/dashboard" class="afs-nav__btn">Главная</Link>
                    <Link href="/profile" class="afs-nav__btn">Профиль</Link>
                    <button class="afs-nav__logout" @click="logout">Выйти</button>
                </nav>
            </div>
        </header>

        <!-- Page content -->
        <main class="afs-main">

            <div class="afs-page-title">
                <h1>Профиль</h1>
                <p>Управляйте своими данными</p>
            </div>

            <!-- Flash -->
            <div v-if="page.props.flash?.success" class="afs-flash">
                ✓ {{ page.props.flash.success }}
            </div>

            <div class="afs-cards">

                <!-- Personal info -->
                <section class="afs-card">
                    <div class="afs-card__header">
                        <span class="afs-card__icon">👤</span>
                        <h2>Личные данные</h2>
                    </div>

                    <form @submit.prevent="saveProfile" class="afs-form">
                        <div class="afs-field">
                            <label for="name">ФИО</label>
                            <input
                                id="name"
                                v-model="profileForm.name"
                                type="text"
                                placeholder="Введите ФИО"
                                :class="{ 'afs-input--error': profileForm.errors.name }"
                            />
                            <span v-if="profileForm.errors.name" class="afs-error">
                                {{ profileForm.errors.name }}
                            </span>
                        </div>

                        <div class="afs-field">
                            <label for="email">Почта</label>
                            <input
                                id="email"
                                v-model="profileForm.email"
                                type="email"
                                placeholder="example@mail.com"
                                :class="{ 'afs-input--error': profileForm.errors.email }"
                            />
                            <span v-if="profileForm.errors.email" class="afs-error">
                                {{ profileForm.errors.email }}
                            </span>
                        </div>

                        <button
                            type="submit"
                            class="afs-btn afs-btn--primary"
                            :disabled="profileForm.processing"
                        >
                            {{ profileForm.processing ? 'Сохранение...' : 'Сохранить' }}
                        </button>
                    </form>
                </section>

                <!-- Password -->
                <section class="afs-card">
                    <div class="afs-card__header">
                        <span class="afs-card__icon">🔒</span>
                        <h2>Смена пароля</h2>
                    </div>

                    <form @submit.prevent="savePassword" class="afs-form">
                        <div class="afs-field">
                            <label for="current_password">Текущий пароль</label>
                            <input
                                id="current_password"
                                v-model="passwordForm.current_password"
                                type="password"
                                placeholder="••••••••"
                                :class="{ 'afs-input--error': passwordForm.errors.current_password }"
                            />
                            <span v-if="passwordForm.errors.current_password" class="afs-error">
                                {{ passwordForm.errors.current_password }}
                            </span>
                        </div>

                        <div class="afs-field">
                            <label for="password">Новый пароль</label>
                            <input
                                id="password"
                                v-model="passwordForm.password"
                                type="password"
                                placeholder="Минимум 8 символов"
                                :class="{ 'afs-input--error': passwordForm.errors.password }"
                            />
                            <span v-if="passwordForm.errors.password" class="afs-error">
                                {{ passwordForm.errors.password }}
                            </span>
                        </div>

                        <div class="afs-field">
                            <label for="password_confirmation">Повторите пароль</label>
                            <input
                                id="password_confirmation"
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                placeholder="••••••••"
                            />
                        </div>

                        <button
                            type="submit"
                            class="afs-btn afs-btn--primary"
                            :disabled="passwordForm.processing"
                        >
                            {{ passwordForm.processing ? 'Сохранение...' : 'Изменить пароль' }}
                        </button>
                    </form>
                </section>

            </div>
        </main>
    </AppLayout>
</template>

<style scoped>
/* ── Header (same as Dashboard) ── */
.afs-header {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d1a00 100%);
    border-bottom: 3px solid #e85c00;
    box-shadow: 0 2px 12px rgba(232, 92, 0, 0.3);
    position: sticky;
    top: 0;
    z-index: 100;
}
.afs-header__inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.afs-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    user-select: none;
}
.afs-logo__icon {
    font-size: 28px;
    filter: drop-shadow(0 0 6px rgba(255, 120, 0, 0.8));
}
.afs-logo__text {
    font-size: 20px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.5px;
    font-family: 'Segoe UI', system-ui, sans-serif;
}
.afs-nav {
    display: flex;
    align-items: center;
    gap: 8px;
}
.afs-nav__btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #e0d6cc;
    text-decoration: none;
    transition: background 0.18s, color 0.18s;
    border: 1px solid transparent;
}
.afs-nav__btn:hover,
.afs-nav__btn.router-link-active {
    background: rgba(232, 92, 0, 0.18);
    color: #ffffff;
    border-color: rgba(232, 92, 0, 0.4);
}
.afs-nav__logout {
    display: inline-flex;
    align-items: center;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #e0d6cc;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    cursor: pointer;
    transition: background 0.18s, color 0.18s, border-color 0.18s;
}
.afs-nav__logout:hover {
    background: rgba(255, 60, 0, 0.2);
    color: #ffffff;
    border-color: rgba(255, 60, 0, 0.5);
}

/* ── Page ── */
.afs-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 24px;
}
.afs-page-title {
    margin-bottom: 32px;
}
.afs-page-title h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 4px;
}
.afs-page-title p {
    color: #777;
    margin: 0;
    font-size: 14px;
}

.afs-flash {
    background: #e6f9ee;
    border: 1px solid #6fcf97;
    color: #1a7a3c;
    border-radius: 10px;
    padding: 12px 18px;
    margin-bottom: 24px;
    font-size: 14px;
    font-weight: 500;
}

.afs-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 24px;
}

/* ── Card ── */
.afs-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
    border: 1px solid #eeece8;
}
.afs-card__header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 24px;
}
.afs-card__icon {
    font-size: 22px;
}
.afs-card__header h2 {
    font-size: 17px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

/* ── Form ── */
.afs-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.afs-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.afs-field label {
    font-size: 13px;
    font-weight: 600;
    color: #444;
}
.afs-field input {
    padding: 10px 14px;
    border: 1.5px solid #ddd;
    border-radius: 10px;
    font-size: 15px;
    color: #1a1a1a;
    background: #fafafa;
    transition: border-color 0.18s, box-shadow 0.18s;
    outline: none;
}
.afs-field input:focus {
    border-color: #e85c00;
    box-shadow: 0 0 0 3px rgba(232, 92, 0, 0.12);
    background: #fff;
}
.afs-input--error {
    border-color: #e53935 !important;
}
.afs-error {
    font-size: 12px;
    color: #e53935;
}

/* ── Button ── */
.afs-btn {
    padding: 11px 22px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: opacity 0.18s, transform 0.12s;
    align-self: flex-start;
}
.afs-btn--primary {
    background: linear-gradient(135deg, #e85c00, #ff8c00);
    color: #fff;
    box-shadow: 0 3px 12px rgba(232, 92, 0, 0.35);
}
.afs-btn--primary:hover:not(:disabled) {
    opacity: 0.92;
    transform: translateY(-1px);
}
.afs-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
