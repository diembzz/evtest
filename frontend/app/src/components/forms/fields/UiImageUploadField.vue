<template>
  <el-upload
    list-type="picture-card"
    :class="{'ui-image-upload-field--hide-uploader': Boolean(modelValue)}"
    :file-list="defaultList()"
    :on-change="handleChange"
    :on-preview="handlePreview"
    :on-remove="handleRemove"
    :auto-upload="false"
    :limit="1"
  >
    <el-icon>
      <Plus/>
    </el-icon>
  </el-upload>

  <el-dialog align-center v-model="preview">
    <img :src="modelValue" alt="Preview Image"/>
  </el-dialog>
</template>

<script lang="ts">
import type {UploadFile, UploadUserFile} from 'element-plus';
import {Plus} from '@element-plus/icons-vue';
import {ElUpload} from 'element-plus';

export default defineNuxtComponent({
  components: {Plus},
  data() {
    return {
      preview: false,
    };
  },
  props: {
    modelValue: String,
  },
  methods: {
    defaultList(): UploadUserFile[] {
      return this.modelValue ? [{status: 'ready', url: this.modelValue, name: this.modelValue}] : [];
    },
    async handleChange(file: UploadFile) {
      this.$emit('update:modelValue', await this.toBase64(file));
    },
    async handleRemove(file: UploadFile) {
      this.$emit('update:modelValue', '');
    },
    async handlePreview(file: UploadFile) {
      this.$emit('update:modelValue', file.url);
      this.preview = true;
    },
    async toBase64(file: UploadFile) {
      return new Promise(async (resolve) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.readAsDataURL(await fetch(file.url).then(res => res.blob()));
      });
    }
  }
})
</script>

<style>
.ui-image-upload-field--hide-uploader .el-upload--picture-card {
  display: none;
}
</style>
