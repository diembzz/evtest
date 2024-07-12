<template>
  <Form :id="Number($route.params.id)"/>
</template>

<script lang="ts">

import Form from "../_form.vue"

export default defineNuxtComponent({
  components: {Form},
})
</script>
