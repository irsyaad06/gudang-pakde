<script setup>
defineProps({
    name: { type: String, required: true },
});

const paths = {
    box: ['m12 3 9 5-9 5-9-5 9-5Z', 'M3 8v9l9 5 9-5V8M12 13v9M7.5 5.5l9 5'],
    grid: ['M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z'],
    plus: ['M12 5v14M5 12h14'],
    search: ['M20.5 20.5 16 16', 'M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z'],
    'arrow-left': ['M19 12H5m6-6-6 6 6 6'],
    'arrow-right': ['M5 12h14m-6-6 6 6-6 6'],
    image: ['M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z', 'm3 16 5-5 5 5 3-3 5 5', 'M9 7h.01'],
    upload: ['M12 16V3m-5 5 5-5 5 5', 'M4 15v4a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4'],
    user: ['M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z', 'M4 21v-2a8 8 0 0 1 16 0v2'],
    settings: ['m9 3-.5 2-2 1-2-.5-2 3.5L4 10.5v3l-1.5 1.5 2 3.5 2-.5 2 1 .5 2h4l.5-2 2-1 2 .5 2-3.5-1.5-1.5v-3L20 9l-2-3.5-2 .5-2-1-.5-2H9Z', 'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
    logout: ['M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4', 'M10 12h11m-5-5 5 5-5 5'],
    close: ['m6 6 12 12M6 18 18 6'],
    check: ['m5 12 4 4L19 6'],
    'chevron-down': ['m6 9 6 6 6-6'],
    edit: ['m15 5 4 4M4 20l4-1L20 7a2.8 2.8 0 0 0-4-4L4 15l-1 6 6-1', 'M13 21h8'],
    trash: ['M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7'],
    location: ['M20 10c0 6-8 11-8 11S4 16 4 10a8 8 0 1 1 16 0Z', 'M15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
    'arrow-up-right': ['M6 18 18 6M6 6h12v12'],
    filter: ['M4 7h9m4 0h3M4 17h3m4 0h9M13 4v6M7 14v6'],
    eye: ['M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z', 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
    whatsapp: ['M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21', 'M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1'],
    'file-text': ['M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z', 'M14 2v6h6', 'M16 13H8', 'M16 17H8', 'M10 9H8'],
    'book-open': ['M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z', 'M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z'],
    server: ['M2 4h20v6H2z', 'M2 14h20v6H2z', 'M6 7h.01', 'M6 17h.01'],
    database: ['M21 5c0 1.66-4 3-9 3s-9-1.34-9-3 4-3 9-3 9 1.34 9 3z', 'M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5', 'M3 12c0 1.66 4 3 9 3s9-1.34 9-3'],
    cpu: ['M4 4h16v16H4z', 'M9 9h6v6H9z', 'M9 1v3', 'M15 1v3', 'M9 20v3', 'M15 20v3', 'M20 9h3', 'M20 14h3', 'M1 9h3', 'M1 14h3'],
    terminal: ['m4 17 6-6-6-6', 'M12 19h8'],
    copy: ['M8 4v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.242a2 2 0 0 0-.602-1.43L16.083 2.57A2 2 0 0 0 14.685 2H10a2 2 0 0 0-2 2z', 'M16 18v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h2'],
    'shield-check': ['M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10', 'm9 12 2 2 4-4'],
    layers: ['M12 2 2 7l10 5 10-5-10-5Z', 'm2 17 10 5 10-5', 'm2 12 10 5 10-5'],
    'chevron-left': ['m15 18-6-6 6-6'],
    'chevron-right': ['m9 18 6-6-6-6'],
    code: ['m16 18 6-6-6-6', 'm8 6-6 6 6 6'],
};
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <path v-for="(path, index) in paths[name] || paths.box" :key="index" :d="path" />
    </svg>
</template>
