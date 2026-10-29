import {defineConfig} from 'vite';

export default defineConfig({
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        target: 'es2022',
        chunkSizeWarningLimit: 10000,
        lib: {entry: 'resources/js/editor.js', formats: ['es'], fileName: () => 'docx-editor.js'},
        rollupOptions: {output: {inlineDynamicImports: true}},
    },
});
