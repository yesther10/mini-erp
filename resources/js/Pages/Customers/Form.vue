<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    pageTitle: {
        type: String,
        required: true,
    },
    submitUrl: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        required: true,
    },
    customer: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    legal_name: props.customer.legal_name,
    cnpj: props.customer.cnpj,
    street: props.customer.street,
    number: props.customer.number,
    district: props.customer.district,
    city: props.customer.city,
    state: props.customer.state,
    zip_code: props.customer.zip_code,
    complement: props.customer.complement,
    primary_contact_name: props.customer.primary_contact_name,
    primary_contact_email: props.customer.primary_contact_email,
    primary_contact_phone: props.customer.primary_contact_phone,
});
const customersPath = '/admin/customers';

const submit = () => {
    form.submit(props.method, props.submitUrl);
};

const fieldClass = 'mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none ring-0 transition focus:border-blue-500';
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Backoffice</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">{{ pageTitle }}</h1>
                <p class="mt-2 text-sm text-slate-600">Capture the legal entity, address, and primary contact in one place.</p>
            </div>

            <Link :href="customersPath" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900">
                Back to list
            </Link>
        </header>

        <form class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" @submit.prevent="submit">
            <div class="grid gap-6 lg:grid-cols-2">
                <label class="block lg:col-span-2">
                    <span class="text-sm font-medium text-slate-700">Legal company name</span>
                    <input v-model="form.legal_name" type="text" :class="fieldClass">
                    <p v-if="form.errors.legal_name" class="mt-2 text-sm text-red-600">{{ form.errors.legal_name }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">CNPJ</span>
                    <input v-model="form.cnpj" type="text" :class="fieldClass" placeholder="00.000.000/0000-00">
                    <p v-if="form.errors.cnpj" class="mt-2 text-sm text-red-600">{{ form.errors.cnpj }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">ZIP code</span>
                    <input v-model="form.zip_code" type="text" :class="fieldClass" placeholder="00000-000">
                    <p v-if="form.errors.zip_code" class="mt-2 text-sm text-red-600">{{ form.errors.zip_code }}</p>
                </label>

                <label class="block lg:col-span-2">
                    <span class="text-sm font-medium text-slate-700">Street</span>
                    <input v-model="form.street" type="text" :class="fieldClass">
                    <p v-if="form.errors.street" class="mt-2 text-sm text-red-600">{{ form.errors.street }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Number</span>
                    <input v-model="form.number" type="text" :class="fieldClass">
                    <p v-if="form.errors.number" class="mt-2 text-sm text-red-600">{{ form.errors.number }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Complement</span>
                    <input v-model="form.complement" type="text" :class="fieldClass">
                    <p v-if="form.errors.complement" class="mt-2 text-sm text-red-600">{{ form.errors.complement }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">District</span>
                    <input v-model="form.district" type="text" :class="fieldClass">
                    <p v-if="form.errors.district" class="mt-2 text-sm text-red-600">{{ form.errors.district }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">City</span>
                    <input v-model="form.city" type="text" :class="fieldClass">
                    <p v-if="form.errors.city" class="mt-2 text-sm text-red-600">{{ form.errors.city }}</p>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">State</span>
                    <input v-model="form.state" type="text" maxlength="2" :class="fieldClass" placeholder="SP">
                    <p v-if="form.errors.state" class="mt-2 text-sm text-red-600">{{ form.errors.state }}</p>
                </label>
            </div>

            <div class="mt-8 border-t border-slate-200 pt-8">
                <h2 class="text-lg font-semibold text-slate-900">Primary contact</h2>
                <div class="mt-6 grid gap-6 lg:grid-cols-3">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Name</span>
                        <input v-model="form.primary_contact_name" type="text" :class="fieldClass">
                        <p v-if="form.errors.primary_contact_name" class="mt-2 text-sm text-red-600">{{ form.errors.primary_contact_name }}</p>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Email</span>
                        <input v-model="form.primary_contact_email" type="email" :class="fieldClass">
                        <p v-if="form.errors.primary_contact_email" class="mt-2 text-sm text-red-600">{{ form.errors.primary_contact_email }}</p>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Phone</span>
                        <input v-model="form.primary_contact_phone" type="text" :class="fieldClass">
                        <p v-if="form.errors.primary_contact_phone" class="mt-2 text-sm text-red-600">{{ form.errors.primary_contact_phone }}</p>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" :disabled="form.processing" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                    Save customer
                </button>
            </div>
        </form>
    </div>
</template>
