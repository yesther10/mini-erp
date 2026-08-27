<script setup>
import { useForm, usePage } from '@inertiajs/vue3';

const flash = usePage().props.flash ?? {};

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};

const fieldClass = 'mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none ring-0 transition focus:border-blue-500';
const panelClass = 'rounded-3xl border border-slate-200 bg-white p-6 shadow-sm';
const submitButtonClass = 'rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60';
</script>

<template>
    <main class="mx-auto flex min-h-screen max-w-5xl flex-col justify-center gap-6 px-6 py-10 lg:px-8">
        <section :class="panelClass" class="mx-auto w-full max-w-xl">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Backoffice</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Sign in</h1>
                <p class="mt-2 text-sm text-slate-600">Use your backoffice credentials to manage customers and assets.</p>
            </div>

            <section v-if="flash.error" class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                {{ flash.error }}
            </section>

            <form class="mt-6" @submit.prevent="submit">
                <div class="grid gap-6">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Email</span>
                        <input v-model="form.email" type="email" autocomplete="username" :class="fieldClass">
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Password</span>
                        <input v-model="form.password" type="password" autocomplete="current-password" :class="fieldClass">
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                    </label>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" :disabled="form.processing" :class="submitButtonClass">
                        Sign in
                    </button>
                </div>
            </form>
        </section>
    </main>
</template>
