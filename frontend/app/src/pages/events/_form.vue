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
      <el-form-item prop="event_date" :error="errors['event_date']" label="Event date">
        <el-date-picker v-model="fields.event_date" clearable aria-label="Pick a date" format="YYYY/MM/DD"
          value-format="YYYY-MM-DD" placeholder="Pick a date"/>
      </el-form-item>
      <el-form-item prop="venue_id" :error="errors['venue_id']" label="Venue">
        <el-select v-model="fields.venue_id" clearable placeholder="Please select event venue">
          <el-option v-for="item in venues" :label="item.name" :value="item.id"/>
        </el-select>
      </el-form-item>
      <el-form-item prop="poster.src" :error="errors['poster.src']" label="Poster">
        <ui-image-upload-field v-model="fields.poster.src"/>
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
      fields: {poster: {src: ''}} as Partial<IEvent>,
      errors: {} as { [key: string]: string },
      venues: [] as { id: number, name: string }[],
      rules: {
        name: [{required: true, message: 'Please input name'}],
        venue_id: [{required: true, message: 'Please select an option'}],
        poster_id: [{required: true, message: 'Please upload poster'}],
        event_date: [{type: 'date', required: true, message: 'Please pick a date'}],
      } as FormRules,
    }
  },

  props: {
    id: Number,
  },

  async mounted() {
    this.fields = this.id ? (await (new Api<IEvent>('events')).show(this.id)) : this.fields;
    this.venues = (await (new Api('venues')).list());
    this.loading = false;
    this.ready = true;
  },

  computed: {
    request(): Partial<IEvent> {
      return {
        id: this.fields.id,
        venue_id: this.fields.venue_id,
        name: this.fields.name,
        event_date: this.fields.event_date,
        poster: {src: this.fields.poster.src},
      };
    }
  },

  methods: {
    async submitForm() {
      await (this.$refs.form as FormInstance).validate(async (valid) => {
        if (valid) {
          this.errors = {};
          this.loading = true;
          const api = new Api('events');
          const res = await api.save(this.request);

          if (res.success()) {
            ElNotification.success({
              title: 'Success',
              message: 'Record is successfully saved.',
              offset: 10,
            });

            if (!this.id) {
              navigateTo('/events/' + res.id);
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
