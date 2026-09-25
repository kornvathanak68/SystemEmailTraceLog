<script setup>
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SimpleLayout from "@/Layouts/SimpleLayout.vue";
import { onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    run: Object,
    records: Object,
    failed: Array,
});

import { router } from "@inertiajs/vue3";

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get("download") === "1") {
        window.location.href = route("runs.excel", props.run.id);

        setTimeout(() => {
            router.visit("/upload");
        }, 1200);
    }
});
</script>

<template>
    <Head :title="`Run #${run.id}`" />

    <SimpleLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Run #{{ run.id }} — {{ run.period }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <a
                    :href="route('runs.excel', run.id)"
                    class="ml-4 inline-block rounded bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700"
                >
                    Download Excel Report
                </a>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dl
                            class="grid grid-cols-[max-content_1fr] gap-x-6 gap-y-2 text-sm"
                        >
                            <dt class="text-gray-500">Stage</dt>
                            <dd>{{ run.stage }}</dd>
                            <dt class="text-gray-500">Status</dt>
                            <dd>{{ run.status }}</dd>
                            <dt class="text-gray-500">Source file</dt>
                            <dd class="break-all">{{ run.source_file }}</dd>
                            <dt class="text-gray-500">Total rows</dt>
                            <dd>{{ run.total_rows }}</dd>
                            <dt class="text-gray-500">Failed</dt>
                            <dd>{{ run.failed_count }}</dd>
                            <dt class="text-gray-500">Error</dt>
                            <dd>{{ run.error ?? "—" }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="font-semibold text-gray-800">
                            Failed Records ({{ failed.length }})
                        </h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Recipient
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Reason
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    PIC
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="rec in failed" :key="rec.id">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ rec.recipient_email }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800"
                                    >
                                        {{ rec.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ rec.failure_reason ?? "—" }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ rec.pic?.name ?? "—" }}
                                </td>
                            </tr>
                            <tr v-if="!failed.length">
                                <td
                                    colspan="4"
                                    class="px-6 py-8 text-center text-sm text-gray-500"
                                >
                                    No failures.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="font-semibold text-gray-800">
                            All Records ({{ records.total }})
                        </h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Message ID
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Recipient
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Subject
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Sent At
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="rec in records.data" :key="rec.id">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ rec.message_id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ rec.recipient_email }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ rec.subject }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ rec.status }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ rec.sent_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </SimpleLayout>
</template>
