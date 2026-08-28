<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    ctaUrl: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 px-6 py-16 text-slate-100">
        <div class="mx-auto flex max-w-6xl flex-col gap-16 lg:flex-row lg:items-center lg:justify-between">
            <section class="max-w-2xl space-y-6">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-400">Mini ERP</p>
                <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                    Manage customers and assets from one admin workspace.
                </h1>
                <p class="max-w-xl text-base leading-7 text-slate-300 sm:text-lg">
                    Keep your customer records, asset catalog, and assignments organized in a secure backoffice built for internal operations.
                </p>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <Link :href="ctaUrl" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                        Open admin dashboard
                    </Link>
                    <Link href="/login" class="inline-flex items-center justify-center rounded-xl border border-white/15 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:border-white/30 hover:text-white">
                        Sign in
                    </Link>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-3 lg:max-w-2xl lg:flex-1">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <p class="text-sm font-medium text-blue-300">Customers</p>
                    <p class="mt-3 text-sm leading-6 text-slate-300">Track legal entities, contacts, and commercial addresses in one place.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <p class="text-sm font-medium text-blue-300">Assets</p>
                    <p class="mt-3 text-sm leading-6 text-slate-300">Register inventory with categories, serial numbers, and lifecycle status.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <p class="text-sm font-medium text-blue-300">Assignments</p>
                    <p class="mt-3 text-sm leading-6 text-slate-300">Allocate assets to customers through a focused internal workflow.</p>
                </div>
            </section>
        </div>
    </div>
</template>
