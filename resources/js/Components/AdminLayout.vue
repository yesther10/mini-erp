<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentUrl = computed(() => page.url);

const navigation = [
    { label: 'Dashboard', href: '/admin/dashboard' },
    { label: 'Customers', href: '/admin/customers' },
    { label: 'Assets', href: '/admin/assets' },
];

function isActive(href) {
    return currentUrl.value.startsWith(href);
}
</script>

<template>
    <div data-testid="admin-layout" class="flex min-h-screen bg-slate-950 text-slate-200">
        <!-- Sidebar -->
        <aside class="flex w-64 flex-col border-r border-white/10 bg-slate-900/50">
            <div class="flex items-center gap-3 border-b border-white/10 px-6 py-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-sm font-bold text-white">M</span>
                <span class="text-lg font-semibold text-white">Mini ERP</span>
            </div>

            <nav class="flex-1 px-3 py-4">
                <ul class="space-y-1">
                    <li v-for="item in navigation" :key="item.href">
                        <Link
                            :href="item.href"
                            :class="[
                                'flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition',
                                isActive(item.href)
                                    ? 'bg-white/10 text-white'
                                    : 'text-slate-400 hover:bg-white/5 hover:text-slate-200',
                            ]"
                        >
                            {{ item.label }}
                        </Link>
                    </li>
                </ul>
            </nav>

            <div class="border-t border-white/10 px-3 py-4">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-slate-200"
                >
                    Logout
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto px-8 py-10">
            <slot />
        </main>
    </div>
</template>
