<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    heroTitle: {
        type: String,
        required: true,
    },
    heroDescription: {
        type: String,
        required: true,
    },
    quoteSubmitUrl: {
        type: String,
        required: true,
    },
    backofficeUrl: {
        type: String,
        required: true,
    },
    assetCategories: {
        type: Array,
        required: true,
    },
});

const flash = usePage().props.flash ?? {};

const form = useForm({
    company_name: '',
    contact_name: '',
    contact_email: '',
    asset_category: props.assetCategories[0]?.value ?? '',
    quantity: 1,
    need_summary: '',
});

const submit = () => {
    form.post(props.quoteSubmitUrl, {
        onSuccess: () => form.reset(),
    });
};

const fieldClass = 'mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none ring-0 transition focus:border-blue-500';
</script>

<template>
    <main class="min-h-screen bg-slate-950 px-6 py-16 text-slate-100">
        <div class="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[minmax(0,1.05fr)_minmax(360px,0.95fr)] lg:items-start">
            <section class="space-y-8">
                <div class="space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-400">Mini ERP</p>
                    <h1 class="max-w-2xl text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                        {{ heroTitle }}
                    </h1>
                    <p class="max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                        {{ heroDescription }}
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="#quote-form" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                        Request a quote
                    </a>
                    <Link :href="backofficeUrl" class="inline-flex items-center justify-center rounded-xl border border-white/15 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:border-white/30 hover:text-white">
                        Backoffice sign in
                    </Link>
                </div>

                <section class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                        <p class="text-sm font-medium text-blue-300">Clear intake</p>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Share the equipment category, quantity, and business context in one request.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                        <p class="text-sm font-medium text-blue-300">Catalog aligned</p>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Choose from the same asset categories our backoffice team manages internally.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                        <p class="text-sm font-medium text-blue-300">Operational follow-up</p>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Your request lands with the internal team that reviews customer and asset demand.</p>
                    </div>
                </section>
            </section>

            <section id="quote-form" class="rounded-3xl border border-white/10 bg-white p-6 text-slate-900 shadow-2xl shadow-slate-950/30">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Quote request</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Tell us what your team needs</h2>
                    <p class="mt-2 text-sm text-slate-600">We capture the essentials first so the backoffice team can respond with the right next step.</p>
                </div>

                <section v-if="flash.success" class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ flash.success }}
                </section>

                <form class="mt-6 space-y-5" @submit.prevent="submit">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Company name</span>
                        <input v-model="form.company_name" type="text" :class="fieldClass">
                        <p v-if="form.errors.company_name" class="mt-2 text-sm text-red-600">{{ form.errors.company_name }}</p>
                    </label>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Contact name</span>
                            <input v-model="form.contact_name" type="text" :class="fieldClass">
                            <p v-if="form.errors.contact_name" class="mt-2 text-sm text-red-600">{{ form.errors.contact_name }}</p>
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Contact email</span>
                            <input v-model="form.contact_email" type="email" :class="fieldClass">
                            <p v-if="form.errors.contact_email" class="mt-2 text-sm text-red-600">{{ form.errors.contact_email }}</p>
                        </label>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-[minmax(0,1fr)_140px]">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Asset category</span>
                            <select v-model="form.asset_category" :class="fieldClass">
                                <option v-for="category in assetCategories" :key="category.value" :value="category.value">{{ category.label }}</option>
                            </select>
                            <p v-if="form.errors.asset_category" class="mt-2 text-sm text-red-600">{{ form.errors.asset_category }}</p>
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Quantity</span>
                            <input v-model="form.quantity" type="number" min="1" :class="fieldClass">
                            <p v-if="form.errors.quantity" class="mt-2 text-sm text-red-600">{{ form.errors.quantity }}</p>
                        </label>
                    </div>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Need summary</span>
                        <textarea v-model="form.need_summary" rows="4" :class="fieldClass"></textarea>
                        <p v-if="form.errors.need_summary" class="mt-2 text-sm text-red-600">{{ form.errors.need_summary }}</p>
                    </label>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-500">The quote request is reviewed by the same backoffice team managing assets and customer records.</p>
                        <button type="submit" :disabled="form.processing" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                            Send quote request
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</template>
