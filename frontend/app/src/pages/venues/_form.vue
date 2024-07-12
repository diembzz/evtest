<template>
  <el-form ref="form"
    v-loading="loading"
    :model="fields"
    :rules="rules"
    status-icon
  >
    <div v-if="ready">
      <el-form-item prop="name" :error="errors['name']" label="Name">
        <el-input v-model="fields.name"/>
      </el-form-item>
      <el-divider/>
      <el-form-item class="app-form-footer">
        <el-button type="primary" @click="submitForm()" :disabled="loading">Save</el-button>
        <el-button @click="resetForm()" :disabled="loading">Reset</el-button>
      </el-form-item>
    </div>
  </el-form>
</template>

<script lang="ts">

import Api from "~/classes/tools/Api";
import type {FormInstance, FormRules} from "element-plus";
import {ElNotification} from "element-plus";
import UiImageUploadField from "~/components/forms/fields/UiImageUploadField.vue";

export default defineNuxtComponent({
  components: {UiImageUploadField},
  data() {
    return {
      ready: false,
      loading: true,
      fields: {poster: {src: ''}} as Partial<IVenue>,
      errors: {} as { [key: string]: string },
      rules: {
        name: [{required: true, message: 'Please input name'}],
      } as FormRules,
    }
  },

  props: {
    id: Number,
  },

  async mounted() {
    this.fields = this.id ? (await (new Api<IVenue>('venues')).show(this.id)) : this.fields;
    this.loading = false;
    this.ready = true;
  },

  computed: {
    request(): Partial<IVenue> {
      return {
        id: this.fields.id,
        name: this.fields.name,
      };
    }
  },

  methods: {
    async submitForm() {
      await (this.$refs.form as FormInstance).validate(async (valid) => {
        if (valid) {
          this.errors = {};
          this.loading = true;
          const api = new Api('venues');
          const res = await api.save(this.request);

          if (res.success()) {
            ElNotification.success({
              title: 'Success',
              message: 'Record is successfully saved.',
              offset: 10,
            });

            if (!this.id) {
              navigateTo('/venues/' + res.id);
            }
          } else {
            this.errors = res.errors();
            if (res.status() != 422) {
              ElNotification.error({
                title: 'Failed',
                message: res.error() as string ?? "API request error.",
                offset: 10,
              })
            }
          }

          this.loading = false;
        }
      })
    },
    resetForm() {
      (this.$refs.form as FormInstance).resetFields();
    }
  }
})
</script>
