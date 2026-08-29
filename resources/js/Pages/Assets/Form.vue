<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    asset: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    internal_code: props.asset.internal_code,
    serial_number: props.asset.serial_number,
    category: props.asset.category,
    brand: props.asset.brand,
    model: props.asset.model,
});
const assetsPath = '/admin/assets';

const submit = () => form.post(assetsPath);

const fieldClass = 'mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none ring-0 transition focus:border-blue-500';
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Backoffice</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Create asset</h1>
                <p class="mt-2 text-sm text-slate-600">Register an asset before allocating it to a customer.</p>
            </div>

            <Link :href="assetsPath" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900">
                Back to list
            </Link>
        </header>

        <form class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" @submit.prevent="submit">
            <div class="grid gap-6 lg:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Internal code</span>
                    <input v-model="form.internal_code" type="text" :class="fieldClass">
                    <p v-if="form.errors.internal_code" class="mt-2 text-sm text-red-600">{{ form.errors.internal_code }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Serial number</span>
                    <input v-model="form.serial_number" type="text" :class="fieldClass">
                    <p v-if="form.errors.serial_number" class="mt-2 text-sm text-red-600">{{ form.errors.serial_number }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Category</span>
                    <select v-model="form.category" :class="fieldClass">
                        <option v-for="category in categories" :key="category.value" :value="category.value">{{ category.label }}</option>
                    </select>
                    <p v-if="form.errors.category" class="mt-2 text-sm text-red-600">{{ form.errors.category }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Brand</span>
                    <input v-model="form.brand" type="text" :class="fieldClass">
                    <p v-if="form.errors.brand" class="mt-2 text-sm text-red-600">{{ form.errors.brand }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Model</span>
                    <input v-model="form.model" type="text" :class="fieldClass">
                    <p v-if="form.errors.model" class="mt-2 text-sm text-red-600">{{ form.errors.model }}</p>
                </label>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" :disabled="form.processing" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                    Save asset
                </button>
            </div>
        </form>

        <p class="text-sm text-slate-600">New assets are created as available. Assignment and later status transitions stay unchanged.</p>
    </div>
</template>
