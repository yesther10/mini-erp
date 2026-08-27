<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    asset: {
        type: Object,
        required: true,
    },
    customers: {
        type: Array,
        required: true,
    },
    assignment: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    customer_id: props.assignment.customer_id,
    allocated_at: props.assignment.allocated_at,
    note: props.assignment.note,
});

const submit = () => form.post(`/assets/${props.asset.id}/assign`);

const fieldClass = 'mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none ring-0 transition focus:border-blue-500';
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Backoffice</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Assign asset</h1>
                <p class="mt-2 text-sm text-slate-600">Allocate {{ asset.internal_code }} to a customer.</p>
            </div>

            <Link href="/assets" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900">
                Back to list
            </Link>
        </header>

        <form class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" @submit.prevent="submit">
            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                {{ asset.brand }} / {{ asset.model }} · {{ asset.category }} · {{ asset.status }}
            </div>

            <p v-if="form.errors.asset" class="mt-4 text-sm text-red-600">{{ form.errors.asset }}</p>

            <div class="mt-6 grid gap-6">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Customer</span>
                    <select v-model="form.customer_id" :class="fieldClass">
                        <option value="">Select a customer</option>
                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.legal_name }}</option>
                    </select>
                    <p v-if="form.errors.customer_id" class="mt-2 text-sm text-red-600">{{ form.errors.customer_id }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Allocation date</span>
                    <input v-model="form.allocated_at" type="date" :class="fieldClass">
                    <p v-if="form.errors.allocated_at" class="mt-2 text-sm text-red-600">{{ form.errors.allocated_at }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Note</span>
                    <textarea v-model="form.note" rows="4" :class="fieldClass" />
                    <p v-if="form.errors.note" class="mt-2 text-sm text-red-600">{{ form.errors.note }}</p>
                </label>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" :disabled="form.processing" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                    Assign asset
                </button>
            </div>
        </form>
    </div>
</template>
