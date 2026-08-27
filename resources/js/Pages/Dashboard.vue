<script setup>
defineProps({
    stats: {
        type: Object,
        required: true,
    },
    customers: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="space-y-8">
        <header>
            <h1 class="text-3xl font-semibold text-white">Dashboard</h1>
            <p class="mt-2 text-sm text-slate-400">Overview of your ERP at a glance.</p>
        </header>

        <!-- Stat cards -->
        <div class="grid gap-6 sm:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                <p class="text-sm font-medium text-slate-400">Total Customers</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ stats.totalCustomers }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                <p class="text-sm font-medium text-slate-400">Total Assets</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ stats.totalAssets }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                <p class="text-sm font-medium text-slate-400">Assigned Assets</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ stats.totalAssigned }}</p>
            </div>
        </div>

        <!-- Customer-asset summary table -->
        <section class="rounded-2xl border border-white/10 bg-white/5 p-6">
            <h2 class="text-lg font-semibold text-white">Customer Asset Summary</h2>

            <div v-if="customers.length === 0" class="mt-4 text-sm text-slate-400">
                No customers registered yet.
            </div>

            <div v-else class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead>
                        <tr class="text-left text-sm font-medium text-slate-400">
                            <th class="pb-3 pr-4">Customer</th>
                            <th class="pb-3 pr-4">Location</th>
                            <th class="pb-3">Assets</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-300">
                        <tr v-for="customer in customers" :key="customer.id">
                            <td class="py-4 pr-4 font-medium text-white">{{ customer.legal_name }}</td>
                            <td class="py-4 pr-4">{{ customer.city }}/{{ customer.state }}</td>
                            <td class="py-4">
                                <span v-if="customer.assets.length === 0" class="text-slate-500">No assets assigned</span>
                                <ul v-else class="space-y-1">
                                    <li v-for="(asset, idx) in customer.assets" :key="idx">
                                        {{ asset.internal_code }} — {{ asset.brand }} {{ asset.model }}
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
