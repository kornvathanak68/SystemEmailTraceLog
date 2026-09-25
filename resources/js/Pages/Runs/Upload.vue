<script setup>
import SimpleLayout from "@/Layouts/SimpleLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";

const period = ref(new Date().toISOString().slice(0, 7));
const file = ref(null);
const processing = ref(false);
const errorMessage = ref("");
const periodError = ref("");

function onFileChange(e) {
    file.value = e.target.files[0];
}

async function submit() {
    errorMessage.value = "";
    periodError.value = "";

    if (!file.value) {
        errorMessage.value = "Please choose a file first.";
        return;
    }

    processing.value = true;

    const formData = new FormData();
    formData.append("period", period.value);
    formData.append("file", file.value);

    try {
        const response = await axios.post("/upload", formData, {
            responseType: "blob",
            headers: { "Content-Type": "multipart/form-data" },
        });

        const blob = new Blob([response.data], {
            type: response.headers["content-type"],
        });

        const disposition = response.headers["content-disposition"] || "";
        const match = disposition.match(/filename="?([^"]+)"?/);
        const filename = match ? match[1] : `TraceResult-${period.value}.xlsx`;

        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

        file.value = null;
        document.getElementById("fileInput").value = "";
    } catch (err) {
        if (err.response && err.response.data instanceof Blob) {
            const text = await err.response.data.text();
            try {
                const json = JSON.parse(text);
                if (json.errors && json.errors.file) {
                    errorMessage.value = json.errors.file[0];
                } else if (json.errors && json.errors.period) {
                    periodError.value = json.errors.period[0];
                } else {
                    errorMessage.value = json.message || "Upload failed.";
                }
            } catch {
                errorMessage.value = "Upload failed.";
            }
        } else {
            errorMessage.value = "Upload failed. Please try again.";
        }
    } finally {
        processing.value = false;
    }
}
</script>

<template>
    <Head title="Upload EA File" />

    <SimpleLayout>
        <template #header>
            <div
                class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl"
            >
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-3.5">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-sm shadow-indigo-200"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h2
                                class="text-2xl font-semibold leading-tight text-slate-900"
                            >
                                Email Trace Log
                            </h2>
                            <p class="mt-0.5 text-sm text-slate-500">
                                Upload and process monthly EA reports
                            </p>
                        </div>
                    </div>

                    <!-- <div
                        class="flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5"
                    >
                        <span class="relative flex h-2 w-2">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"
                            ></span>
                        </span>
                        <span class="text-xs font-medium text-emerald-700">
                            System online
                        </span>
                    </div> -->
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg"
                >
                    <div
                        class="border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-gray-200 px-8 py-6"
                    >
                        <h3 class="text-xl font-semibold text-white">
                            EA File Upload
                        </h3>
                        <p class="mt-1 text-sm text-indigo-100">
                            Select a reporting period and upload the
                            corresponding Excel file.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-8 p-8">
                        <div>
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
                                Reporting Period
                            </label>

                            <input
                                v-model="period"
                                type="month"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />

                            <p
                                v-if="periodError"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ periodError }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
                                EA Excel File
                            </label>

                            <div
                                class="rounded-2xl border-2 border-dashed border-indigo-300 bg-indigo-50/40 p-8 text-center transition hover:border-indigo-500 hover:bg-indigo-50"
                            >
                                <svg
                                    class="mx-auto h-12 w-12 text-indigo-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"
                                    />
                                </svg>

                                <p class="mt-3 text-sm text-gray-600">
                                    Upload your Excel report file
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Supported formats: XLSX, XLS, CSV
                                </p>

                                <input
                                    id="fileInput"
                                    type="file"
                                    accept=".xlsx,.xls,.csv"
                                    @change="onFileChange"
                                    class="mt-5 block w-full rounded-lg border border-gray-300 bg-white text-sm file:mr-4 file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-white hover:file:bg-indigo-700"
                                />

                                <div
                                    v-if="file"
                                    class="mt-4 rounded-lg bg-white p-3 text-sm text-gray-700 shadow-sm"
                                >
                                    Selected:
                                    <span class="font-semibold">{{
                                        file.name
                                    }}</span>
                                </div>
                            </div>

                            <p
                                v-if="errorMessage"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ errorMessage }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-blue-200 bg-blue-50 p-4"
                        >
                            <h4 class="font-semibold text-blue-900">
                                Upload Guidelines
                            </h4>
                            <ul
                                class="mt-2 list-disc space-y-1 pl-5 text-sm text-blue-800"
                            >
                                <li>
                                    Ensure the file matches the selected period.
                                </li>
                                <li>
                                    Only Excel (.xlsx, .xls) and CSV files are
                                    accepted.
                                </li>
                                <li>
                                    Duplicate uploads may overwrite previous
                                    data.
                                </li>
                            </ul>
                        </div>

                        <div
                            class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end"
                        >
                            <button
                                type="button"
                                @click="
                                    file = null;
                                    errorMessage = '';
                                    periodError = '';
                                "
                                class="rounded-xl hover:scale-95 transform transition-all border border-gray-300 bg-white px-5 py-2.5 text-center text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                :disabled="processing"
                                class="rounded-xl hover:scale-95 transform bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{
                                    processing
                                        ? "Processing Upload..."
                                        : "Upload & Process"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </SimpleLayout>
</template>
