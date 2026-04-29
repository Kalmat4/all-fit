<script setup>
import { Link, router } from '@inertiajs/vue3'

const logout = () => {
    router.post('/logout')
}
</script>

<template>
    <div class="min-vh-100" style="background: #f0f4f0;">
        <slot />
    </div>
</template>