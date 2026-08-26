<script setup>
defineProps({
    appName: {
        type: String,
        default: 'Mini ERP',
    },
});

const foundationItems = [
    'Laravel 13 application scaffolded for the ERP prototype',
    'Vue 3 frontend mounted through Inertia without a separate SPA API layer',
    'Docker Compose workflow prepared for PHP, Nginx, MySQL, Composer, and NPM',
];

const nextSteps = [
    'Start Vite in watch mode to iterate on the UI',
    'Add the first ERP business module on top of this foundation',
    'Expand the ERP with additional operational modules after the first validated slice',
];
</script>

<template>
    <main class="mx-auto flex min-h-screen max-w-6xl flex-col gap-12 px-6 py-12 lg:px-8">
        <section class="grid gap-8 rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/30 lg:grid-cols-[1.4fr_0.8fr]">
            <div class="space-y-6">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-300">ERP Prototype Foundation</p>
                <div class="space-y-4">
                    <h1 class="text-4xl font-semibold tracking-tight text-white lg:text-5xl">
                        {{ appName }} is running on Laravel, Vue 3, and Inertia.
                    </h1>
                    <p class="max-w-2xl text-base leading-7 text-slate-300">
                        This landing page proves the backend, frontend, and server-driven SPA bridge are wired together in a single pragmatic MVP setup.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 text-sm text-slate-200">
                    <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-4 py-2">Laravel</span>
                    <span class="rounded-full border border-sky-400/30 bg-sky-400/10 px-4 py-2">Vue 3</span>
                    <span class="rounded-full border border-violet-400/30 bg-violet-400/10 px-4 py-2">Inertia</span>
                    <span class="rounded-full border border-amber-400/30 bg-amber-400/10 px-4 py-2">Docker</span>
                    <span class="rounded-full border border-rose-400/30 bg-rose-400/10 px-4 py-2">MySQL</span>
                </div>
            </div>

            <aside class="rounded-2xl border border-white/10 bg-slate-900/80 p-6">
                <p class="text-sm font-medium text-slate-400">Suggested local commands</p>
                <div class="mt-4 space-y-3 font-mono text-sm text-slate-200">
                    <div class="rounded-xl bg-slate-950 px-4 py-3">docker compose up -d mysql app nginx</div>
                    <div class="rounded-xl bg-slate-950 px-4 py-3">docker compose run --rm app php artisan migrate</div>
                    <div class="rounded-xl bg-slate-950 px-4 py-3">docker compose run --rm --service-ports app npm run dev</div>
                </div>
            </aside>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                <h2 class="text-xl font-semibold text-white">What is already in place</h2>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-300">
                    <li v-for="item in foundationItems" :key="item" class="flex gap-3">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        <span>{{ item }}</span>
                    </li>
                </ul>
            </article>

            <article class="rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                <h2 class="text-xl font-semibold text-white">Immediate next steps</h2>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-300">
                    <li v-for="item in nextSteps" :key="item" class="flex gap-3">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-400"></span>
                        <span>{{ item }}</span>
                    </li>
                </ul>
            </article>
        </section>
    </main>
</template>
