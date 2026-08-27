<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    customers: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const flash = usePage().props.flash ?? {};

const submitSearch = (event) => {
    const formData = new FormData(event.target);
    const search = formData.get('search')?.toString().trim() ?? '';

    router.get('/customers', { search }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Backoffice</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Customers</h1>
                <p class="mt-2 text-sm text-slate-600">Manage legal entities, commercial addresses, and primary contacts.</p>
            </div>

            <Link href="/customers/create" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">
                New customer
            </Link>
        </header>

        <section v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ flash.success }}
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form class="flex flex-col gap-3 lg:flex-row" @submit.prevent="submitSearch">
                <input
                    name="search"
                    :value="filters.search"
                    type="text"
                    placeholder="Search by legal name or CNPJ"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none ring-0 transition focus:border-blue-500"
                >
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                    Search
                </button>
            </form>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="text-left text-sm font-semibold text-slate-600">
                            <th class="pb-3">Legal name</th>
                            <th class="pb-3">CNPJ</th>
                            <th class="pb-3">Location</th>
                            <th class="pb-3">Primary contact</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <tr v-for="customer in customers.data" :key="customer.id">
                            <td class="py-4 pr-4 font-medium text-slate-900">{{ customer.legal_name }}</td>
                            <td class="py-4 pr-4">{{ customer.cnpj }}</td>
                            <td class="py-4 pr-4">{{ customer.city }}/{{ customer.state }}</td>
                            <td class="py-4 pr-4">
                                <div>{{ customer.primary_contact_name }}</div>
                                <div class="text-xs text-slate-500">{{ customer.primary_contact_email }}</div>
                            </td>
                            <td class="py-4 text-right">
                                <Link :href="`/customers/${customer.id}/edit`" class="font-semibold text-blue-600 hover:text-blue-500">
                                    Edit
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="customers.data.length === 0">
                            <td colspan="5" class="py-10 text-center text-sm text-slate-500">
                                No customers found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
