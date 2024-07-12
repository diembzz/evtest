// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  ssr: false,
  srcDir: 'src/',
  devtools: {enabled: false},
  compatibilityDate: '2024-07-09',
  css: ['../public/css/style.css'],
  modules: [
    '@element-plus/nuxt',
  ],
})
