<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    assets: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const flash = usePage().props.flash ?? {};

const submitSearch = (event) => {
    const formData = new FormData(event.target);

    router.get('/assets', {
        search: formData.get('search')?.toString().trim() ?? '',
        category: formData.get('category')?.toString() ?? '',
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const formatLabel = (value) => value.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase());
</script>

<template>
    <main class="mx-auto flex min-h-screen max-w-6xl flex-col gap-6 px-6 py-10 lg:px-8">
        <header class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Backoffice</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Assets</h1>
                <p class="mt-2 text-sm text-slate-600">Register assets and allocate them to customers from one internal catalog.</p>
            </div>

            <Link href="/assets/create" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">
                New asset
            </Link>
        </header>

        <section v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ flash.success }}
        </section>

        <section v-if="flash.error" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ flash.error }}
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form class="grid gap-3 lg:grid-cols-[1fr_220px_auto]" @submit.prevent="submitSearch">
                <input
                    name="search"
                    :value="filters.search"
                    type="text"
                    placeholder="Search by internal code"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none ring-0 transition focus:border-blue-500"
                >

                <select name="category" :value="filters.category ?? ''" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none ring-0 transition focus:border-blue-500">
                    <option value="">All categories</option>
                    <option v-for="category in categories" :key="category.value" :value="category.value">
                        {{ category.label }}
                    </option>
                </select>

                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                    Search
                </button>
            </form>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="text-left text-sm font-semibold text-slate-600">
                            <th class="pb-3">Internal code</th>
                            <th class="pb-3">Category</th>
                            <th class="pb-3">Brand / model</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Customer</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <tr v-for="asset in assets" :key="asset.id">
                            <td class="py-4 pr-4 font-medium text-slate-900">
                                <div>{{ asset.internal_code }}</div>
                                <div v-if="asset.serial_number" class="text-xs text-slate-500">{{ asset.serial_number }}</div>
                            </td>
                            <td class="py-4 pr-4">{{ formatLabel(asset.category) }}</td>
                            <td class="py-4 pr-4">{{ asset.brand }} / {{ asset.model }}</td>
                            <td class="py-4 pr-4">{{ formatLabel(asset.status) }}</td>
                            <td class="py-4 pr-4">{{ asset.customer ?? '—' }}</td>
                            <td class="py-4 text-right">
                                <Link v-if="asset.can_assign" :href="`/assets/${asset.id}/assign`" class="font-semibold text-blue-600 hover:text-blue-500">
                                    Assign
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="assets.length === 0">
                            <td colspan="6" class="py-10 text-center text-sm text-slate-500">
                                No assets found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</template>
