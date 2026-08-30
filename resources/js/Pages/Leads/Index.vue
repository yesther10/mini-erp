<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    leads: {
        type: Object,
        required: true,
    },
});

const leadsPath = '/admin/leads';
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Backoffice</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Leads</h1>
                <p class="mt-2 text-sm text-slate-600">Review inbound quote requests from potential customers.</p>
            </div>
        </header>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="text-left text-sm font-semibold text-slate-600">
                            <th class="pb-3">Company</th>
                            <th class="pb-3">Contact</th>
                            <th class="pb-3">Category</th>
                            <th class="pb-3">Quantity</th>
                            <th class="pb-3">Need Summary</th>
                            <th class="pb-3">Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <tr v-for="lead in leads.data" :key="lead.id">
                            <td class="py-4 pr-4 font-medium text-slate-900">{{ lead.company_name }}</td>
                            <td class="py-4 pr-4">
                                <div>{{ lead.contact_name }}</div>
                                <div class="text-xs text-slate-500">{{ lead.contact_email }}</div>
                            </td>
                            <td class="py-4 pr-4 capitalize">{{ lead.asset_category }}</td>
                            <td class="py-4 pr-4">{{ lead.quantity }}</td>
                            <td class="py-4 pr-4 max-w-xs truncate">{{ lead.need_summary }}</td>
                            <td class="py-4 text-sm text-slate-500">{{ new Date(lead.created_at).toLocaleDateString() }}</td>
                        </tr>
                        <tr v-if="leads.data.length === 0">
                            <td colspan="6" class="py-10 text-center text-sm text-slate-500">
                                No quote requests yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
